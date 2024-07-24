<?php

namespace SilverStripe\TagManager\SnippetProvider;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\TagManager\SnippetProvider;
use SilverStripe\View\ArrayData;

/**
 * A snippet provider that lets you add Matomo JS
 */
class MatomoSnippetProvider implements SnippetProvider
{
    const OPT_OUT_NONE = 'none';

    const OPT_OUT_LOCATIONS = [
        self::OPT_OUT_NONE => 'None',
        self::ZONE_BODY_START => 'Top',
        self::ZONE_BODY_END => 'Bottom'
    ];

    public function getTitle()
    {
        return "Matomo";
    }

    public function getParamFields()
    {
        return FieldList::create(
            TextField::create(
                'AnalyticsURL',
                _t(static::class . '.AnalyticsURL', 'Analytics URL')
            ),
            TextField::create(
                'SiteID',
                _t(static::class . '.SiteID', 'Site ID')
            ),
            DropdownField::create(
                'OptOut',
                _t(static::class . 'OptOut', 'Generate opt-out notice'),
                self::OPT_OUT_LOCATIONS
            ),
            CheckboxField::create(
                'DisableCookies',
                _t(static::class . 'DisableCookies', 'Disable all tracking cookies')
            )->setDescription('https://matomo.org/faq/general/faq_157/')
        );
    }

    public function getSummary(array $params)
    {
        if (!empty($params['SiteID'])) {
            return $this->getTitle() . " -  " . $params['SiteID'];
        } else {
            return $this->getTitle();
        }
    }

    public function getSnippets(array $params)
    {
        if (empty($params['AnalyticsURL'])) {
            throw new \InvalidArgumentException("Please supply your Analytics URL");
        }
        if (empty($params['SiteID'])) {
            throw new \InvalidArgumentException("Please supply Site ID");
        }

        $optout  = self::OPT_OUT_NONE;
        $cookies = true;

        if (!empty($params['OptOut'])) {
            $optout = $params['OptOut'];
        }

        if (
            !empty($params['DisableCookies'])
            && (bool)$params['DisableCookies'] === true
        ) {
            $cookies = false;
        }

        // Strip out any http/https and remove trailing slashes
        $url = preg_replace('#^[^:/.]*[:/]+#i', '', $params['AnalyticsURL']);
        $url = rtrim($url,"/");
        // Sanitise the ID
        $site_id = preg_replace('[^A-Za-z0-9_\-]', '', $params['SiteID']);

        $data = ArrayData::create([
            'URL' => $url,
            'SiteID' => $site_id,
            'Cookies' => $cookies
        ]);

        $script = $data->renderWith(static::class . '_script');
        $script = str_replace("\n", "", $script);

        $noscript = $data->renderWith(static::class . '_noscript');
        $noscript = str_replace("\n", "", $noscript);

        $notice = "";
        $body_start = "";
        $body_end = "";

        if ($optout != self::OPT_OUT_NONE) {
            $notice = $data->renderWith(static::class . '_optout');
        }

        if ($optout === self::ZONE_BODY_START) {
            $body_start = $notice;
            $body_end = $noscript;
        }

        if ($optout === self::ZONE_BODY_END) {
            $body_end = $notice . $noscript;
        }

        return [
            self::ZONE_HEAD_START => $script,
            self::ZONE_BODY_START => $body_start,
            self::ZONE_BODY_END => $body_end
        ];
    }
}

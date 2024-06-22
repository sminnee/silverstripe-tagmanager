<?php

namespace SilverStripe\TagManager\SnippetProvider;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\TagManager\SnippetProvider;
use SilverStripe\View\ArrayData;

/**
 * A snippet provider that lets you add Matomo JS
 */
class MatomoSnippetProvider implements SnippetProvider
{

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
            CheckboxField::create(
                'DoNotTrack',
                _t(static::class . 'DoNotTrack', 'Honor Do Not Track headers')
            )->setDescription('https://en.wikipedia.org/wiki/Do_Not_Track'),
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

        $dnt = false;
        $cookies = true;

        if (
            !empty($params['DoNotTrack'])
            && (bool)$params['DoNotTrack'] === true
        ) {
            $dnt = true;
        }

        if (
            !empty($params['DisableCookies'])
            && (bool)$params['DisableCookies'] === true
        ) {
            $cookies = false;
        }

        // Strip out any http/https
        $url = preg_replace('#^[^:/.]*[:/]+#i', '', $params['AnalyticsURL']);
        // Sanitise the ID
        $site_id = preg_replace('[^A-Za-z0-9_\-]', '', $params['SiteID']);

        $data = ArrayData::create([
            'URL' => $url,
            'SiteID' => $site_id,
            'DoNotTrack' => $dnt,
            'Cookies' => $cookies
        ]);

        $script = $data->renderWith(static::class . '_script');
        $script = str_replace("\n", "", $script);

        $noscript = $data->renderWith(static::class . '_noscript');
        $noscript = str_replace("\n", "", $noscript);

        return [
            self::ZONE_HEAD_START => $script,
            self::ZONE_BODY_END => $noscript
        ];
    }
}

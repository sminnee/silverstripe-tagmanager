<?php

namespace SilverStripe\TagManager\SnippetProvider;

use SilverStripe\TagManager\SnippetProvider;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms;
use SilverStripe\Core\Manifest\ModuleResourceLoader;

/**
 * A snippet provider that lets you add arbitrary HTML
 */
class GoogleTagManagerSnippetProvder implements SnippetProvider
{

    public function getTitle()
    {
        return "Google Tag Manager";
    }

    public function getParamFields()
    {

        $imgSrc = ModuleResourceLoader::singleton()->resolveURL('sminnee/tagmanager:client/img/gtm-guide.png');

        return new FieldList(
            (new Forms\TextField("GTMID", "GTM ID"))->setDescription("Will look like 'GTM-XXXXXX'"),
            new Forms\LiteralField("HelpImage", "<p><img src=\"$imgSrc\" style=\"width: 100%; border-radius: 30px; box-shadow: 2px 2px 20px #CCC;\"></p>")
        );

    }

    public function getSummary(array $params)
    {
        if (!empty($params['GTMID'])) {
            return $this->getTitle() . " -  " . $params['GTMID'];
        } else {
            return $this->getTitle();
        }
    }

    public function getSnippets(array $params)
    {
        if (empty($params['GTMID'])) {
            throw new \InvalidArgumentException("Please supply GTM ID");
        }

        // Sanitise the ID
        $accountID = trim(preg_replace('[^A-Za-z0-9_\-]', '', $params['GTMID']));

        $head = <<<HTML
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','$accountID');</script>
<!-- End Google Tag Manager -->
HTML;

        $body = <<<HTML
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=$accountID"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
HTML;

        return [
            self::ZONE_HEAD_START => $head,
            self::ZONE_BODY_START => $body,
        ];
    }
}

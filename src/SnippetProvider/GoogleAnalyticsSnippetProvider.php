<?php

namespace SilverStripe\TagManager\SnippetProvider;

use SilverStripe\TagManager\SnippetProvider;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms;

/**
 * A snippet provider that lets you add arbitrary HTML
 */
class GoogleAnalyticsSnippetProvider implements SnippetProvider
{

    public function getTitle()
    {
        return "Google Analytics";
    }

    public function getParamFields()
    {
        return new FieldList(
            new Forms\TextField("GoogleAnalyticsID", "Google Analytics ID")
        );
    }

    public function getSummary(array $params)
    {
        if (!empty($params['GoogleAnalyticsID'])) {
            return $this->getTitle() . " -  " . $params['GoogleAnalyticsID'];
        } else {
            return $this->getTitle();
        }
    }

    public function getSnippets(array $params)
    {
        if (empty($params['GoogleAnalyticsID'])) {
            throw new \InvalidArgumentException("Please supply Google Analytics ID");
        }

        // Sanitise the ID
        $gaId = preg_replace('[^A-Za-z0-9_\-]', '', $params['GoogleAnalyticsID']);

        $content = <<<HTML
<script async src="https://www.googletagmanager.com/gtag/js?id=$gaId"></script>
<script>
window.dataLayer=window.dataLayer||[];
function gtag(){dataLayer.push(arguments);}
gtag('js',new Date());
gtag('config','$gaId');
</script>
HTML;

        $content = str_replace("\n", "", $content);

        return [
            'start-head' => $content,
        ];
    }
}

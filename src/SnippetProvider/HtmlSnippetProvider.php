<?php

namespace SilverStripe\TagManager\SnippetProvider;

use SilverStripe\TagManager\SnippetProvider;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms;

/**
 * A snippet provider that lets you add arbitrary HTML
 */
class HtmlSnippetProvider implements SnippetProvider
{

    public function getTitle()
    {
        return "Arbitrary HTML";
    }

    public function getParamFields()
    {
        $zones = [
            "start-head" => "After <HEAD>",
            "end-head" => "Before </HEAD>",
            "start-body" => "After <BODY>",
            "end-body" => "Before </BODY>",
        ];

        return new FieldList(
            new Forms\DropdownField("Zone", "Zone", $zones),
            new Forms\TextAreaField("Content", "HTML Content")
        );
    }

    public function getSummary(array $params)
    {
        return $this->getTitle() . " in  " . $params['Zone'];
    }

    public function getSnippets(array $params)
    {
        if (empty($params['Zone']) || empty($params['Content'])) {
            throw new \InvalidArgumentException("Please supply both Zone and Content");
        }

        return [
            $params['Zone'] => $params['Content']
        ];
    }
}

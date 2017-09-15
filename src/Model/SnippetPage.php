<?php

namespace SilverStripe\TagManager\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\CMS\Model\SiteTree;


/**
 * Represents the attachment of a configured snippet to a single page or section
 */
class SnippetPage extends DataObject
{

    private static $db = [
        "AppliesTo" => "Enum('section,page', 'section')",
    ];

    private static $has_one = [
        "Snippet" => Snippet::class,
        "Page" => SiteTree::class,
    ];
}

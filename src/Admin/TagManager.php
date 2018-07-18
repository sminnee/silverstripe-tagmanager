<?php

namespace SilverStripe\TagManager\Admin;

use Snippet;
use ModelAdmin;

class TagManager extends ModelAdmin
{
    private static $managed_models = [ Snippet::class ];

    private static $menu_title = 'Tag Manager';

    private static $url_segment = 'tagmanager';
}

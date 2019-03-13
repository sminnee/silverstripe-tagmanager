<?php

namespace SilverStripe\TagManager\Admin;

use SilverStripe\TagManager\Model\Snippet;
use SilverStripe\Admin\ModelAdmin;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class TagManager extends ModelAdmin
{

    public $showImportForm = false;

    private static $managed_models = [ Snippet::class ];

    private static $menu_title = 'Tag Manager';

    private static $url_segment = 'tagmanager';

    public function getEditForm($id = null, $fields = null)
    {
        $fields = parent::getEditForm();

        $grid = $fields->Fields()->fieldByName("SilverStripe-TagManager-Model-Snippet");

        $grid->getConfig()->addComponent(new GridFieldOrderableRows("Sort"));

        return $fields;
    }
}

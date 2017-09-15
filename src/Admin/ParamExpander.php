<?php

namespace SilverStripe\TagManager\Admin;

use SilverStripe\Forms\FieldList;

/**
 * Provides support for adding JSON-packed parameters into a single DataObject field
 */
trait ParamExpander
{

    protected function expandParams($modelField, $paramFields, FieldList $fields, $tabName)
    {
        $fields->removeByName($modelField);
        if ($paramFields) {
            foreach ($paramFields as $field) {
                $name = "COMPOUND_{$modelField}_" . $field->getName();
                $field->setName($name);
                $field->setValue($this->getField($name));
                if ($tabName) {
                    $fields->addFieldToTab($tabName, $field);
                } else {
                    $fields->push($field);
                }
            }
        }
    }

    function getField($key) {
        // Compound field handler
        if(substr($key,0,9)=='COMPOUND_') {
            list($dummy, $field, $subfield) = explode('_', $key, 3);
            $json = json_decode(parent::getField($field), true);
            if(isset($json[$subfield])) return $json[$subfield];

        } else {
            return parent::getField($key);
        }
    }

    function setField($key, $val) {
        // Compound field handler
        if(substr($key,0,9)=='COMPOUND_') {
            list($dummy, $field, $subfield) = explode('_', $key, 3);
            $json = json_decode(parent::getField($field), true);
            $json[$subfield] = $val;
            return parent::setField($field, json_encode($json));

        } else {
            return parent::setField($key, $val);
        }
    }

}

<?php

namespace SilverStripe\TagManager\Extension;

use SS_HTTPRequest;
use Director;
use Snippet;
use Extension;
use DBField;

/**
 * ContentController extension that inserts configured snippets
 */
class TagInserter extends Extension
{

    public function afterCallActionHandler(SS_HTTPRequest $request, $action, $response)
    {
        $context = null;
        if ($this->owner->hasMethod('data')) {
            $context =$this->owner->data();
        }

        $response->setValue($this->insertSnippetsIntoHTML($response->getValue(), $context));
        return $response;
    }

    protected function insertSnippetsIntoHTML($html)
    {
        // TO DO: work out how to get info on current page
        $snippets = Snippet::get()->filter(['Active' => 'on']);

        $combinedHTML = [];

        foreach ($snippets as $snippet) {
            $thisHTML = $snippet->getSnippets();
            foreach ($thisHTML as $k => $v) {
                if (!isset($combinedHTML[$k])) {
                    $combinedHTML[$k] = "";
                }
                $combinedHTML[$k] .= $v;
            }
        }

        foreach ($combinedHTML as $k => $v) {
            switch ($k) {
                case 'start-head':
                    $html = preg_replace('#(<head( [^>]*)?>)#i', '\\1' . $v, $html);
                    break;

                case 'end-head':
                    $html = preg_replace('#(</head[ >])#i', $v . '\\1', $html);
                    break;

                case 'start-body':
                    $html = preg_replace('#(<body( [^>]*)?>)#i', '\\1' . $v, $html);
                    break;

                case 'end-body':
                    $html = preg_replace('#(</body[ >])#i', $v . '\\1', $html);
                    break;
            }
        }

        return $html;
    }
}

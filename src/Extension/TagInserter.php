<?php

namespace SilverStripe\TagManager\Extension;

use HTTPRequest;
use Director;
use SilverStripe\TagManager\Model\Snippet;
use Extension;
use DBField;

/**
 * ContentController extension that inserts configured snippets
 */
class TagInserter extends Extension
{

    public function afterCallActionHandler(HTTPRequest $request, $action, DBField $response)
    {
        $response->setValue($this->insertSnippetsIntoHTML($response->getValue(), $this->owner->data()));
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
                    $html = preg_replace('#(<head[^>]*>)#i', '\\1' . $v, $html);
                    break;

                case 'end-head':
                    $html = preg_replace('#(</head)#i', $v . '\\1', $html);
                    break;

                case 'start-body':
                    $html = preg_replace('#(<body[^>]*>)#i', '\\1' . $v, $html);
                    break;

                case 'end-body':
                    $html = preg_replace('#(</body)#i', $v . '\\1', $html);
                    break;
            }
        }

        return $html;
    }
}

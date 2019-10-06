<?php

namespace SilverStripe\TagManager\Extension;

use Prs\Log\LoggerInterface;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Control\Middleware\HTTPMiddleware;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Director;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\TagManager\Model\Snippet;
use SilverStripe\TagManager\SnippetProvider;

/**
 * ContentController extension that inserts configured snippets
 */
class TagInserter extends Extension
{

    public function afterCallActionHandler(HTTPRequest $request, $action, $response)
    {
        if ($response instanceof DBField) {
            $response->setValue($this->insertSnippetsIntoHTML($response->getValue(), $this->owner->data()));
        }
        return $response;
    }

    protected function insertSnippetsIntoHTML($html)
    {
        // TO DO: work out how to get info on current page
        $snippets = Snippet::get()->filter(['Active' => 'on']);

        $combinedHTML = [];

        $logger = null;
        if (Injector::inst()->has(LoggerInterface::class)) {
            $logger = Injector::inst()->get(LoggerInterface::class);
        }

        foreach ($snippets as $snippet) {
            try {
                $thisHTML = $snippet->getSnippets();
            } catch (\InvalidArgumentException $e) {
                $message = sprintf(
                    "Misconfigured snippet %s: %s",
                    $snippet->getTitle(),
                    $e->getMessage()
                );

                if ($logger) {
                    $logger->warning($message, ['exception' => $e]);
                } else {
                    user_error($message, E_USER_WARNING);
                }

                continue;
            }

            foreach ($thisHTML as $k => $v) {
                if (!isset($combinedHTML[$k])) {
                    $combinedHTML[$k] = "";
                }
                $combinedHTML[$k] .= $v;
            }
        }

        foreach ($combinedHTML as $k => $v) {
            switch ($k) {
                case SnippetProvider::ZONE_HEAD_START:
                    $html = preg_replace('#(<head(>+|[\s]+(.*)?>))#i', '\\1' . $v, $html);
                    break;

                case SnippetProvider::ZONE_HEAD_END:
                    $html = preg_replace('#(</head(>+|[\s]+(.*)?>))#i', $v . '\\1', $html);
                    break;

                case SnippetProvider::ZONE_BODY_START:
                    $html = preg_replace('#(<body(>+|[\s]+(.*)?>))#i', '\\1' . $v, $html);
                    break;

                case SnippetProvider::ZONE_BODY_END:
                    $html = preg_replace('#(</body(>+|[\s]+(.*)?>))#i', $v . '\\1', $html);
                    break;

                default:
                    $message = "Unknown snippet zone '$k'; ignoring";
                    if ($logger) {
                        $logger->warning($message, ['exception' => $e]);
                    } else {
                        user_error($message, E_USER_WARNING);
                    }
            }
        }

        return $html;
    }
}

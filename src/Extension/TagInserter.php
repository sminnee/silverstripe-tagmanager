<?php

namespace SilverStripe\TagManager\Extension;

use Prs\Log\LoggerInterface;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Core\Extension;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\TagManager\Model\Snippet;
use SilverStripe\TagManager\SnippetProvider;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\DevBuildController;
use SilverStripe\Dev\DevelopmentAdmin;
use SilverStripe\Security\Security;

/**
 * ContentController extension that inserts configured snippets
 */
class TagInserter extends Extension
{
    /**
     * List of controllers that will be ignored by
     * by TagInserter globally.
     * 
     * NOTE: TagInsert will ignore ALL instances of
     * the below controllers.
     * 
     * @var array
     */
    private static $ignored_controllers = [
        LeftAndMain::class,
        DevBuildController::class,
        DevelopmentAdmin::class,
        Security::class
    ];

    public function afterCallActionHandler(HTTPRequest $request, $action, $response)
    {
        /** @var Controller */
        $owner = $this->getOwner();
        $ignored = Config::inst()
            ->get(static::class, 'ignored_controllers');

        if (!$owner instanceof Controller) {
            return $response;
        }

        foreach ($ignored as $baseclass) {
            if (is_a($owner, $baseclass)) {
                return $response;
            }
        }

        $data = null;

        if ($owner->hasMethod('data')) {
            $data = $owner->data();
        }

        if ($response instanceof DBField) {
            $response->setValue(
                $this->insertSnippetsIntoHTML($response->getValue()),
                $data
            );
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

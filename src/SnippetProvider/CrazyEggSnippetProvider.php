<?php

namespace SilverStripe\TagManager\SnippetProvider;

use SilverStripe\TagManager\SnippetProvider;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms;
use SilverStripe\Core\Manifest\ModuleResourceLoader;

/**
 * A snippet provider that lets you add arbitrary HTML
 */
class CrazyEggSnippetProvider implements SnippetProvider
{

    public function getTitle()
    {
        return "Crazy Egg";
    }

    public function getParamFields()
    {

        $imgSrc = ModuleResourceLoader::singleton()->resolveURL('sminnee/tagmanager:client/img/crazyegg-guide.png');

        return new FieldList(
            (new Forms\TextField("AccountID", "Your account number"))
                ->setDescription("Will look like '01234567'. Find it in Account > Your profile in the Crazy Egg app"),
            new Forms\LiteralField(
                "HelpImage",
                "<p><img src=\"$imgSrc\" style=\"width: 100%; border-radius: 30px; box-shadow: 2px 2px 20px #CCC;\"></p>"
            )
        );

    }

    public function getSummary(array $params)
    {
        if (!empty($params['AccountID'])) {
            return $this->getTitle() . " -  " . $params['AccountID'];
        } else {
            return $this->getTitle();
        }
    }

    public function getSnippets(array $params)
    {
        if (empty($params['AccountID'])) {
            throw new \InvalidArgumentException("Please supply your account number");
        }

        // Sanitise the ID
        $accountID = trim(preg_replace('[^A-Za-z0-9_\-]', '', $params['AccountID']));

        if (strlen($accountID) != 8) {
            throw new \InvalidArgumentException("Account numbers must be 8 digits");
        }

        $slashedID = substr($accountID, 0, 4) . '/' . substr($accountID, 4);

        $head = <<<HTML
<script type="text/javascript" src="//script.crazyegg.com/pages/scripts/$slashedID.js" async="async"></script>
HTML;

        return [
            'start-head' => $head,
        ];
    }
}

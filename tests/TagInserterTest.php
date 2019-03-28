<?php

namespace SilverStripe\TagManager\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\TagManager\Model\Snippet;
use SilverStripe\TagManager\Extension\TagInserter;
use SilverStripe\TagManager\SnippetProvider\HtmlSnippetProvider;
use ReflectionMethod;

class TagInserterTest extends SapphireTest
{
    protected $usesDatabase = true;

    protected function setUp()
    {
        parent::setUp();

        $targets = [
            'start-head' => '<!-- START HEAD -->',
            'end-head' => '<!-- END HEAD -->',
            'start-body' => '<!-- START BODY -->',
            'end-body' => '<!-- END BODY -->',
        ];

        foreach ($targets as $zone => $content) {
            (new Snippet([
                'SnippetClass' => HtmlSnippetProvider::class,
                'Active' => 'on',
                'SnippetParams' => json_encode([
                    'Zone' => $zone,
                    'Content' => $content,
                ]),
            ]))->write();
        }
    }

    public function testInsertSnippetsIntoHTML()
    {
        $tagInserter = new TagInserter();

        $method = new ReflectionMethod($tagInserter, 'insertSnippetsIntoHTML');
        $method->setAccessible(true);

        $html = $this->getTestHtml();
        $response = $method->invoke($tagInserter, $html);

        $expected = <<<HTML
<!DOCTYPE html>
<html>
    <head><!-- START HEAD -->
        <title>Test</title>
    <!-- END HEAD --></head>
    <body><!-- START BODY -->
        <header>
            <h1>Title</h1>
        </header>
        <main>
            <h2>Page Title</h2>
            <p>Content</p>
        </main>
        <footer>
            <a href="#">Home</a>
        </footer>
    <!-- END BODY --></body>
</html>
HTML;

        $this->assertEquals($expected, $response);
    }

    private function getTestHtml()
    {
        return <<<HTML
<!DOCTYPE html>
<html>
    <head>
        <title>Test</title>
    </head>
    <body>
        <header>
            <h1>Title</h1>
        </header>
        <main>
            <h2>Page Title</h2>
            <p>Content</p>
        </main>
        <footer>
            <a href="#">Home</a>
        </footer>
    </body>
</html>
HTML;
    }
}

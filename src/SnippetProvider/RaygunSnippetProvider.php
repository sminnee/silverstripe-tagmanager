<?php declare(strict_types=1);

namespace SilverStripe\TagManager\SnippetProvider;

use SilverStripe\Core\Environment;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\TagManager\SnippetProvider;
use SilverStripe\View\HTML;

/**
 * A snippet provider that lets you add Raygun for the front-end JavaScript
 */
class RaygunSnippetProvider implements SnippetProvider
{
    public function getTitle() : string
    {
        return "Raygun crash reporting (front-end JavaScript only)";
    }

    /**
     * Returns three fields:
     *  - ENV_APP_KEY, which is a checkbox allowing reuse of silverstripe/raygun module integration
     *  - USER_APP_KEY, which is a text input letting users to define raygun api key manually
     *  - HelpImage, which is a simple screenshot of the raygun UI showing where to look for the API key
     *
     * Only returns ENV_APP_KEY field if SS_RAYGUN_APP_KEY is defined,
     * which is a prerequisite for silverstripe/raygun module
     *
     * {@inheritdoc}
     */
    public function getParamFields() : FieldList
    {
        $apiKeyDefined = !empty(Environment::getEnv('SS_RAYGUN_APP_KEY'));

        $imgSrc = ModuleResourceLoader::singleton()->resolveURL('sminnee/tagmanager:client/img/raygun-guide.png');

        $fields = [];
        if ($apiKeyDefined) {
            $fields[] = CheckboxField::create('ENV_APP_KEY', 'Use "silverstripe/raygun" module')
                ->setDescription('Choose to reuse "silverstripe/raygun" module settings. This is the default and you want this in most cases');
        }

        if ($apiKeyDefined) {
            $apiKeyTitle = 'API Key (only if you didn\'t select the checkbox above)';
        } else {
            $apiKeyTitle = 'API Key';
        }

        $fields[] = TextField::create('USER_APP_KEY', $apiKeyTitle)->setDescription('Will look like alphanumeric password');
        $fields[] = LiteralField::create('HelpImage', HTML::createTag('p', [], "<img src=\"$imgSrc\" style=\"width: 100%; border-radius: 30px; box-shadow: 2px 2px 20px #CCC;\">"));

        return new FieldList(...$fields);
    }

    public function getSummary(array $params) : string
    {
        return $this->getTitle();
    }

    public function getSnippets(array $params) : array
    {
        if (isset($params['ENV_APP_KEY']) && $params['ENV_APP_KEY']) {
            $apiKey = Environment::getEnv('SS_RAYGUN_APP_KEY');
        } elseif (isset($params['USER_APP_KEY'])) {
            $apiKey = $params['USER_APP_KEY'];
        } else {
            $apiKey = null;
        }

        $snippet = <<<HTML
<!-- Raygun -->
<script type="text/javascript">
  !function(a,b,c,d,e,f,g,h){a.RaygunObject=e,a[e]=a[e]||function(){
  (a[e].o=a[e].o||[]).push(arguments)},f=b.createElement(c),g=b.getElementsByTagName(c)[0],
  f.async=1,f.src=d,g.parentNode.insertBefore(f,g),h=a.onerror,a.onerror=function(b,c,d,f,g){
  h&&h(b,c,d,f,g),g||(g=new Error(b)),a[e].q=a[e].q||[],a[e].q.push({
  e:g})}}(window,document,"script","//cdn.raygun.io/raygun4js/raygun.min.js","rg4js");
</script>
<!-- End Raygun -->

<!-- Raygun crash reporting -->
<script type="text/javascript">
  rg4js('apiKey', '$apiKey');
  rg4js('enableCrashReporting', true);
</script>
<!-- End Raygun crash reporting -->
HTML;

        if (empty($apiKey)) {
            $snippet = '<!-- Raygun: no configuration found -->';
        }

        return [
            self::ZONE_HEAD_START => $snippet,
        ];
    }
}

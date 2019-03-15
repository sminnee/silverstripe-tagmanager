# SilverStripe Tag Manager

SilverStripe's server-side answer to Google/Adobe Tag Manager!

SilverStripe tag manager provides a framework for adding programmatically-generated snippets of HTML to pages on your site.
Examples include tracking codes for analytics and other packages and meta-tags.

To get full benefit of tag manager you should find or create modules that provide such snippets. CMS administrators can use
the Tag Manager admin section to enable and configure these snippets.

## Benefits

 * Developers of SilverStripe modules that add front-end snippets to each page can write less code
 * Different snippets can be configured & actvitated by CMS administrators
 * Don't need to coordinate a developer release to get snippets added or removed
 * Unused snippets can be disabled by the administrator, reducing page weight

## Features

 * Add site-wide snippets
 * Add section-specific or page-specific snippets (coming soon)
 * Developers: Build custom snippets by implementing the SnippetProvider interface provided

Out of the box the following snippets are available

 * Raw HTML, added to the head or the end of the body
 * Google Analytics
 * Google Tag Manager
 * [Crazy Egg](https://www.crazyegg.com/)

## Add-on modules

Tag Manager is only as useful as its extension modules, have a look at [modules that list it as a dependency](https://packagist.org/packages/sminnee/tagmanager/dependents) to get some inspiration!

## Creating your own tag

To create a new type of tag, you will need to write a PHP class that implements the `SilverStripe\TagManager\SnippetProvider` interface. A simple example is [GoogleAnalyticsSnippetProvider](src/SnippetProvider/GoogleAnalyticsSnippetProvider.php).

Your class will need the following methods for helping configure the admin UI:

 *  `getTitle()`: Return the title of this snippet provider for admin UIs
 *  `getSummary(array $params)`: Return a short description of the configured snippet. Params will be an map of param name => param value
 *  `getParamFields()`: Return a `FieldList` for configuring the snippet. Each field should return a scalar value, so no GridFields sorry!

And this method to actually generate the snippet:

 * `getSnippets(array $params)`: Return an map of snippet "zone" to snippet content.

Each snippet should be placed in a "zone". Zones are predefined insertion points within the overall page. Allowed zones are "start-head", "end-head", "start-body", "end-body". Your snippet provider can return more than 1 of these.

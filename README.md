SilverStripe Tag Manager
========================

SilverStripe's server-side answer to Google/Adobe Tag Manager!

SilverStripe tag manager provides a framework for adding programmatically-generated snippets of HTML to pages on your site.
Examples include tracking codes for analytics and other packages and meta-tags.

To get full benefit of tag manager you should find or create modules that provide such snippets. CMS administrators can use
the Tag Manager admin section to enable and configure these snippets.

## Benefits

 * Developers of SilverStripe modules that add front-end snippets to each page can write less code
 * Different snippets can be configured & actvitated by CMS administrators

## Features

 * Add site-wide snippets
 * Add section-specific or page-specific snippets
 * Build custom snippets by implementing the SnippetProvider interface provided

Out of the box the following snippets are available

 * Raw HTML, added to the head or the end of the body
 * Metatag

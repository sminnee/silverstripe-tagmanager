<?php

namespace SilverStripe\TagManager;

/**
 * Code that can provide snippets for the tag manager should implement this interface.
 * Snippets may have user-provided parameters that are used to generate the snippet.
 * These can be configured and enabled in the admin/tagmanager UI.
 */
interface SnippetProvider
{

    /**
     * Return the title of this snippet provider for admin UIs
     */
    public function getTitle();

    /**
     * Return a short description of the configured snippet
     */
    public function getSummary(array $params);

    /**
     * Return a list of fields for configuring this snippet.
     * The each field should return a scalar value (sorry, no GridFields)
     *
     * @return FieldList
     */
    public function getParamFields();

    /**
     * Return the snippets to insert into page.
     *
     * Each snippet should be placed in a "zone". Zones are predefined insertion points within the overall page.
     * Allowed zones are "start-head", "end-head", "start-body", "end-body".
     *
     * @param $params A map of parameters to configure the snippet provider with. The keys passed must correspond to
     *                the names of the fields returned by getParamFields().
     *
     * @return A map of zone => HTML.
     * @throws \InvalidArugmentException If the params are not correct.
     */
    public function getSnippets(array $params);

}

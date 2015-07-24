<?php

namespace Syrma\WebContainer\Util;

/**
 * Manage the error pages.
 */
interface ErrorPageLoaderInterface
{
    /**
     * Load the error page content.
     *
     * @param $statusCode
     *
     * @return resource
     */
    public function loadContentByStatusCode($statusCode);
}

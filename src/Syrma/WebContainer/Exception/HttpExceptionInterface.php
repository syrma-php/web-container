<?php


namespace Syrma\WebContainer\Exception;

/**
 * Http specific exception
 */
interface HttpExceptionInterface {

    /**
     * Return the HTTP status code
     *
     * @return int
     */
    public function getStatusCode();

    /**
     * Return the response headers
     *
     * @return array
     */
    public function getHeaders();
}
<?php

namespace Syrma\WebContainer\Exception;

use Psr\Http\Message\ResponseInterface;

/**
 * Exception with response.
 */
interface ResponseAwareExceptionInterface
{
    /**
     * @return ResponseInterface
     */
    public function getResponse();
}

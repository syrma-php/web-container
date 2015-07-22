<?php

namespace Syrma\WebContainer\Exception;

use Psr\Http\Message\ResponseInterface;

/**
 * Special exception for server stop.
 */
interface ServerStopExceptionInterface
{
    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return ResponseInterface
     */
    public function getResponse();
}

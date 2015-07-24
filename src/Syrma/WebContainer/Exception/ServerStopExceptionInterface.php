<?php

namespace Syrma\WebContainer\Exception;

/**
 * Special exception for server stop.
 */
interface ServerStopExceptionInterface
{
    /**
     * @return string
     */
    public function getMessage();
}

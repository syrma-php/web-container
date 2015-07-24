<?php

namespace Syrma\WebContainer;

use Syrma\WebContainer\Exception\ResponseAwareExceptionInterface;
use Syrma\WebContainer\Exception\ServerStopExceptionInterface;

/**
 * Handle exceptions.
 */
interface ExceptionHandlerInterface
{
    /**
     * Handle the exception or throw continue.
     *
     * @param \Exception $exception
     *
     * @throws ResponseAwareExceptionInterface - send the contained response to client
     * @throws ServerStopExceptionInterface    - stop the actual server/thread
     * @throws \Exception
     */
    public function handle(\Exception $exception);
}

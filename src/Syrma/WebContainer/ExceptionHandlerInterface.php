<?php

namespace Syrma\WebContainer;

use Psr\Http\Message\ResponseInterface;
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
     * @throws ServerStopExceptionInterface    - stop the actual server/thread
     * @throws ResponseAwareExceptionInterface - send the contained response to client
     * @throws \Exception
     */
    public function handle(\Exception $exception);

    /**
     * Create the error page for handled exceptions.
     *
     * @param \Exception|null $exception
     *
     * @return ResponseInterface
     */
    public function createErrorResponseByException(\Exception $exception = null);
}

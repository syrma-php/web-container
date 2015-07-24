<?php

namespace Syrma\WebContainer;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Syrma\WebContainer\Exception\ResponseAwareExceptionInterface;
use Syrma\WebContainer\Exception\ServerStopExceptionInterface;

/**
 * Handle the server requests.
 */
interface RequestHandlerInterface
{
    /**
     * Handle the server request.
     *
     * @param RequestInterface $request
     *
     * @throws ResponseAwareExceptionInterface - send the contained response to client
     * @throws ServerStopExceptionInterface    - stop the actual server/thread
     *
     * @return ResponseInterface
     */
    public function handle(RequestInterface $request);

    /**
     * Call the finished request.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @throws ServerStopExceptionInterface - stop the actual server/thread
     */
    public function finish(RequestInterface $request, ResponseInterface $response);
}

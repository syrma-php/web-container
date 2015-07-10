<?php

namespace Syrma\WebContainer;

/**
 *
 */
interface ServerInterface
{
    /**
     * Start the server.
     *
     * @param ServerContextInterface  $context
     * @param RequestHandlerInterface $requestHandler
     */
    public function start(ServerContextInterface $context, RequestHandlerInterface $requestHandler);

    /**
     * Stop the server.
     */
    public function stop();
}

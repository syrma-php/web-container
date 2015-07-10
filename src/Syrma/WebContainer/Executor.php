<?php

namespace Syrma\WebContainer;

use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Syrma\WebContainer\RequestHandler\CallbackRequestHandler;

/**
 * Executor of server.
 */
class Executor
{
    /**
     * @var ServerInterface
     */
    private $server;

    /**
     * @var RequestHandlerInterface;
     */
    private $requestHandler;

    /**
     * @var LoggerInterface|NULL
     */
    private $logger;

    /**
     * @param ServerInterface         $server
     * @param RequestHandlerInterface $requestHandler
     * @param LoggerInterface         $logger
     *
     * @internal param ServerContextInterface $context
     */
    public function __construct(
        ServerInterface $server,
        RequestHandlerInterface $requestHandler,
        LoggerInterface $logger = null
    ) {
        $this->server = $server;
        $this->requestHandler = $requestHandler;
        $this->logger = $logger;
    }

    /**
     * Start the server.
     *
     * @param ServerContextInterface $context
     */
    public function execute(ServerContextInterface $context)
    {
        $this->server->start(
            $context,
            $this->decorateRequestHandler($this->requestHandler)
        );
    }

    /**
     * @param RequestHandlerInterface $requestHandler
     *
     * @return CallbackRequestHandler
     */
    private function decorateRequestHandler(RequestHandlerInterface $requestHandler)
    {
        return new CallbackRequestHandler(function (RequestInterface $request) use ($requestHandler) {
            // TODO - error handling
            return $requestHandler->handle($request);
        });
    }
}

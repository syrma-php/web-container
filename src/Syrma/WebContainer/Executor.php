<?php


namespace Syrma\WebContainer;


use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Syrma\WebContainer\RequestHandler\CallbackRequestHandler;

/**
 * Executor of server
 */
class Executor {

    /**
     * @var ServerInterface
     */
    private $server;

    /**
     * @var ServerContextInterface
     */
    private $context;

    /**
     * @var RequestHandlerInterface;
     */
    private $requestHandler;

    /**
     * @var LoggerInterface|NULL
     */
    private $logger;


    /**
     * @param ServerInterface $server
     * @param ServerContextInterface $context
     * @param RequestHandlerInterface $requestHandler
     * @param LoggerInterface $logger
     */
    public function __construct(
        ServerInterface $server,
        ServerContextInterface $context,
        RequestHandlerInterface $requestHandler,
        LoggerInterface $logger = NULL
    )
    {
        $this->server           = $server;
        $this->context          = $context;
        $this->requestHandler   = $requestHandler;
        $this->logger           = $logger;
    }


    /**
     * Start the server
     */
    public function execute()
    {

        $this->server->start(
            $this->context,
            $this->decorateRequestHandler( $this->requestHandler )
        );
    }

    /**
     * @param RequestHandlerInterface $requestHandler
     *
     * @return CallbackRequestHandler
     */
    private function decorateRequestHandler(RequestHandlerInterface $requestHandler )
    {
        return new CallbackRequestHandler(function( RequestInterface $request ) use ($requestHandler){
            // TODO - error handling
            return $requestHandler->handle($request);
        });
    }


}
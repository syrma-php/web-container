<?php

namespace Syrma\WebContainer;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Syrma\WebContainer\Exception\ServerStopExceptionInterface;
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
     * @var int
     */
    private $masterPid;

    /**
     * @param ServerInterface         $server
     * @param RequestHandlerInterface $requestHandler
     * @param LoggerInterface         $logger
     */
    public function __construct(
        ServerInterface $server,
        RequestHandlerInterface $requestHandler,
        LoggerInterface $logger = null
    ) {
        if (true !== $server->isAvaiable()) {
            throw new \InvalidArgumentException(sprintf(
                'The server(%s) is not avaiable!',
                get_class($server)
            ));
        }

        $this->server = $server;
        $this->requestHandler = $requestHandler;
        $this->logger = $logger;
        $this->masterPid = posix_getpid();
    }

    /**
     * Start the server.
     *
     * @param ServerContextInterface $context
     */
    public function execute(ServerContextInterface $context)
    {
        $this->logServerStart($context);

        $this->server->start(
            $context,
            $this->decorateRequestHandler($this->requestHandler)
        );
    }

    /**
     * @param ServerContextInterface $context
     */
    private function logServerStart(ServerContextInterface $context)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf(
                'The server(%s:%s) started(pid:%s)!',
                $context->getListenAddress(),
                $context->getListenPort(),
                posix_getpid()
            ), array(
                'serverClass' => get_class($this->server),
                'requestHandlerClass' => get_class($this->requestHandler),
            ));
        }
    }

    /**
     * @param ServerStopExceptionInterface $ex
     */
    private function logServerStop(ServerStopExceptionInterface $ex)
    {
        if (null !== $this->logger) {
            $this->logger->notice(sprintf(
                'The server(masterPid: %s, pid: %s) stopped: %s',
                $this->masterPid,
                posix_getpid(),
                $ex->getMessage()
            ));
        }
    }

    /**
     * @param \Exception $ex
     */
    private function logException(\Exception $ex)
    {
        if (null !== $this->logger) {
            $this->logger->critical(sprintf(
                'The server(pid: %s) stopped with unexpected exception(%s): %s',
                posix_getpid(),
                get_class($ex),
                $ex->getMessage()
            ), array(
                'exception' => (string) $ex,
            ));
        }
    }

    /**
     * @param RequestHandlerInterface $requestHandler
     *
     * @return CallbackRequestHandler
     */
    private function decorateRequestHandler(RequestHandlerInterface $requestHandler)
    {
        return new CallbackRequestHandler(
            function (RequestInterface $request) use ($requestHandler) {

                try {
                    return $requestHandler->handle($request);
                } catch (ServerStopExceptionInterface $ex) {
                    $this->logServerStop($ex);
                    posix_kill(posix_getpid(), SIGTERM); //trigger stop
                    return $ex->getResponse();
                } catch (\Exception $ex) {
                    $this->logException($ex);
                    throw $ex;
                }

            },
            function (RequestInterface $request, ResponseInterface $response) use ($requestHandler) {
                $requestHandler->finish($request, $response);
                pcntl_signal_dispatch();
            }
        );
    }
}

<?php

namespace Syrma\WebContainer;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Syrma\WebContainer\Exception\ResponseAwareExceptionInterface;
use Syrma\WebContainer\Exception\ServerStopExceptionInterface;
use Syrma\WebContainer\RequestHandler\CallbackRequestHandler;

/**
 * Executor of server.
 */
class Executor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var ServerInterface
     */
    private $server;

    /**
     * @var RequestHandlerInterface;
     */
    private $requestHandler;

    /**
     * @var ExceptionHandlerInterface
     */
    private $exceptionHandler;

    /**
     * @param ServerInterface           $server
     * @param RequestHandlerInterface   $requestHandler
     * @param ExceptionHandlerInterface $exceptionHandler
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        ServerInterface $server,
        RequestHandlerInterface $requestHandler,
        ExceptionHandlerInterface $exceptionHandler
    ) {
        if (true !== $server->isAvaiable()) {
            throw new \InvalidArgumentException(sprintf(
                'The server(%s) is not avaiable!',
                get_class($server)
            ));
        }
        $this->server = $server;
        $this->requestHandler = $requestHandler;
        $this->exceptionHandler = $exceptionHandler;

        set_time_limit(0); // no time limit
    }

    /**
     * Start the server.
     *
     * @param ServerContextInterface|NULL $context
     */
    public function execute(ServerContextInterface $context = null)
    {
        if (null === $context) {
            $context = new ServerContext();
        }

        $this->logServerStart($context);

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
        return new CallbackRequestHandler(
            function (RequestInterface $request) use ($requestHandler) {

                $ex = null;
                try {
                    $response = $requestHandler->handle($request);
                } catch (\Exception $ex) {
                    $response = $this->handleException($ex);
                }

                return isset($response) ? $response : $this->exceptionHandler->createErrorResponseByException($ex);
            },
            function (RequestInterface $request, ResponseInterface $response) use ($requestHandler) {

                try {
                    $requestHandler->finish($request, $response);
                } catch (\Exception $ex) {
                    $this->handleException($ex);
                }
            }
        );
    }

    /**
     * @param ServerStopExceptionInterface $ex
     */
    private function stopServerByException(ServerStopExceptionInterface $ex)
    {
        $this->logServerStop($ex);
        $this->server->stop();
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger()
    {
        if (null === $this->logger) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * @param ServerContextInterface $context
     */
    private function logServerStart(ServerContextInterface $context)
    {
        $this->getLogger()->info(sprintf(
            'The server(%s:%s) started(pid:%s)!',
            $context->getListenAddress(),
            $context->getListenPort(),
            posix_getpid()
        ), array(
            'serverClass' => get_class($this->server),
            'requestHandlerClass' => get_class($this->requestHandler),
        ));
    }

    /**
     * @param ServerStopExceptionInterface $ex
     */
    private function logServerStop(ServerStopExceptionInterface $ex)
    {
        $this->getLogger()->notice(sprintf(
            'The server(pid: %s) stopped: %s',
            posix_getpid(),
            $ex->getMessage()
        ), array(
            'exception' => $ex,
        ));
    }

    /**
     * @param \Exception $ex
     */
    private function logException(\Exception $ex)
    {
        $this->getLogger()->error(sprintf(
            'Internal handled exception: %s',
            $ex->getMessage()
        ), array(
            'exception' => $ex,
        ));
    }

    /**
     * @param \Exception $ex
     *
     * @throws \Exception
     *
     * @return ResponseInterface|null
     */
    private function handleException(\Exception $ex)
    {
        if (null !== $response = $this->handleInternalException($ex)) {
            $this->logException($ex);

            return $response;
        }

        try {
            $this->exceptionHandler->handle($ex);
        } catch (\Exception $ex) {
            if (null !== $response = $this->handleInternalException($ex)) {
                return $response;
            }
        }

        throw $ex;
    }

    /**
     * @param \Exception $ex
     *
     * @return ResponseInterface|null
     */
    private function handleInternalException(\Exception $ex)
    {
        $response = null;

        if ($ex instanceof ResponseAwareExceptionInterface) {
            $response = $ex->getResponse();
        }

        if ($ex instanceof ServerStopExceptionInterface) {
            $this->stopServerByException($ex);
        }

        return $response;
    }
}

<?php

namespace Syrma\WebContainer;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Syrma\WebContainer\Exception\DefaultExceptionHandler;
use Syrma\WebContainer\RequestHandler\CallbackRequestHandler;
use Syrma\WebContainer\Server\React\ReactMessageTransformer;
use Syrma\WebContainer\Server\React\ReactServer;
use Syrma\WebContainer\Server\Swoole\SwooleMessageTransformer;
use Syrma\WebContainer\Server\Swoole\SwooleServer;
use Syrma\WebContainer\Server\Swoole\SwooleServerOptions;
use Syrma\WebContainer\Util\ErrorPageLoader;
use Syrma\WebContainer\Util\ErrorPageLoaderInterface;
use Syrma\WebContainer\Util\Psr7FactoryInterface;
use Syrma\WebContainer\Util\ZendPsr7Factory;

/**
 * Container for all component.
 */
class Container
{
    /**
     * @var Psr7FactoryInterface|null
     */
    private $psr7Factory;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var ExceptionHandlerInterface|null
     */
    private $exceptionHandler;

    /**
     * @var ErrorPageLoaderInterface|null
     */
    private $errorPageLoader;

    /**
     * @var ServerInterface|null
     */
    private $server;

    /**
     * @param Psr7FactoryInterface $psr7Factory
     */
    public function setPsr7Factory(Psr7FactoryInterface $psr7Factory)
    {
        $this->psr7Factory = $psr7Factory;
    }

    /**
     * @return Psr7FactoryInterface
     */
    public function getPsr7Factory()
    {
        if (null === $this->psr7Factory) {
            $this->psr7Factory = new ZendPsr7Factory();
        }

        return $this->psr7Factory;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if (null === $this->logger) {
            $this->logger = new Logger(
                'SyrmaWebContainer',
                array(new StreamHandler(fopen('php://stdout', 'w')))
            );
        }

        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return ExceptionHandlerInterface
     */
    public function getExceptionHandler()
    {
        if (null === $this->exceptionHandler) {
            $this->exceptionHandler = new DefaultExceptionHandler($this->getPsr7Factory(), $this->getErrorPageLoader());

            if ($this->exceptionHandler instanceof LoggerAwareInterface) {
                $this->exceptionHandler->setLogger($this->getLogger());
            }
        }

        return $this->exceptionHandler;
    }

    /**
     * @param ExceptionHandlerInterface $exceptionHandler
     */
    public function setExceptionHandler(ExceptionHandlerInterface $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
    }

    /**
     * @return ErrorPageLoaderInterface
     */
    public function getErrorPageLoader()
    {
        if (null === $this->errorPageLoader) {
            $this->errorPageLoader = ErrorPageLoader::createDefault();
        }

        return $this->errorPageLoader;
    }

    /**
     * @param ErrorPageLoaderInterface $errorPageLoader
     */
    public function setErrorPageLoader(ErrorPageLoaderInterface $errorPageLoader)
    {
        $this->errorPageLoader = $errorPageLoader;
    }

    /**
     * @return ServerInterface
     */
    public function getServer()
    {
        if (null === $this->server) {
            if (true === SwooleServer::isAvaiable()) {
                $this->server = $this->createSwooleServer();
            } elseif (true === ReactServer::isAvaiable()) {
                $this->server = $this->createReactServer();
            } else {
                throw new \BadMethodCallException('The server instance not avaiable!');
            }
        }

        return $this->server;
    }

    /**
     * @param ServerInterface $server
     */
    public function setServer(ServerInterface $server)
    {
        $this->server = $server;
    }

    /**
     * @param array $options
     *
     * @return SwooleServer
     */
    public function createSwooleServer(array $options = array())
    {
        if (array() === $options) {
            $options = array(
                'daemonize' => false,
                'worker_num' => 1,
            );
        }

        return new SwooleServer(
            new SwooleMessageTransformer($this->getPsr7Factory()),
            new SwooleServerOptions($options)
        );
    }

    /**
     * @return ReactServer
     */
    public function createReactServer()
    {
        return new ReactServer(new ReactMessageTransformer($this->getPsr7Factory()));
    }

    /**
     * @param RequestHandlerInterface|callable $requestHandler
     *
     * @return Executor
     */
    public function createExecutor($requestHandler)
    {
        if (is_callable($requestHandler)) {
            $requestHandler = new CallbackRequestHandler($requestHandler);
        } elseif (!$requestHandler instanceof RequestHandlerInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Expected argument of type "RequestHandlerInterface" or "callable", "%s" given',
                is_object($requestHandler) ? get_class($requestHandler) : gettype($requestHandler))
            );
        }

        $executor = new Executor($this->getServer(), $requestHandler, $this->getExceptionHandler());
        $executor->setLogger($this->getLogger());

        return $executor;
    }
}

<?php

namespace Syrma\WebContainer\Tests;

use Psr\Log\LoggerInterface;
use Syrma\WebContainer\Container;
use Syrma\WebContainer\ExceptionHandlerInterface;
use Syrma\WebContainer\Executor;
use Syrma\WebContainer\RequestHandlerInterface;
use Syrma\WebContainer\Server\React\ReactServer;
use Syrma\WebContainer\Server\Swoole\SwooleServer;
use Syrma\WebContainer\ServerInterface;
use Syrma\WebContainer\Util\ErrorPageLoaderInterface;
use Syrma\WebContainer\Util\Psr7FactoryInterface;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Container
     */
    protected function createContainer()
    {
        return new Container();
    }

    public function testPsr7Factory()
    {
        $container = $this->createContainer();
        $this->assertInstanceOf(Psr7FactoryInterface::class, $container->getPsr7Factory());

        /** @var Psr7FactoryInterface $mockFactory */
        $mockFactory = $this->getMock(Psr7FactoryInterface::class);
        $container->setPsr7Factory($mockFactory);
        $this->assertSame($mockFactory, $container->getPsr7Factory());
    }

    public function testLogger()
    {
        $container = $this->createContainer();
        $this->assertInstanceOf(LoggerInterface::class, $container->getLogger());

        /** @var LoggerInterface $logger */
        $logger = $this->getMock(LoggerInterface::class);
        $container->setLogger($logger);
        $this->assertSame($logger, $container->getLogger());
    }

    public function testExceptionHandler()
    {
        $container = $this->createContainer();
        $this->assertInstanceOf(ExceptionHandlerInterface::class, $container->getExceptionHandler());

        /** @var ExceptionHandlerInterface $exceptionHandler */
        $exceptionHandler = $this->getMock(ExceptionHandlerInterface::class);
        $container->setExceptionHandler($exceptionHandler);
        $this->assertSame($exceptionHandler, $container->getExceptionHandler());
    }

    public function testErrorPageLoader()
    {
        $container = $this->createContainer();
        $this->assertInstanceOf(ErrorPageLoaderInterface::class, $container->getErrorPageLoader());

        /** @var ErrorPageLoaderInterface $errorPageLoader */
        $errorPageLoader = $this->getMock(ErrorPageLoaderInterface::class);
        $container->setErrorPageLoader($errorPageLoader);
        $this->assertSame($errorPageLoader, $container->getErrorPageLoader());
    }

    public function testServer()
    {
        $container = $this->createContainer();
        $this->assertInstanceOf(ServerInterface::class, $container->getServer());

        /** @var ServerInterface $server */
        $server = $this->getMock(ServerInterface::class);
        $container->setServer($server);
        $this->assertSame($server, $container->getServer());
    }

    public function testSwooleServer()
    {
        $server = $this->createContainer()->createSwooleServer();
        $this->assertInstanceOf(SwooleServer::class, $server);
    }

    public function testReactServer()
    {
        $server = $this->createContainer()->createReactServer();
        $this->assertInstanceOf(ReactServer::class, $server);
    }

    public function testExecutor()
    {
        $this->assertInstanceOf(
            Executor::class,
            $this->createContainer()->createExecutor(function () {})
        );

        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $this->getMock(RequestHandlerInterface::class);
        $this->assertInstanceOf(
            Executor::class,
            $this->createContainer()->createExecutor($requestHandler)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExcutorBad()
    {
        $this->createContainer()->createExecutor('cica');
    }
}

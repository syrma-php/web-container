<?php

namespace Syrma\WebContainer\Tests;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Syrma\WebContainer\Exception\ServerStopWithResponseException;
use Syrma\WebContainer\ExceptionHandlerInterface;
use Syrma\WebContainer\Executor;
use Syrma\WebContainer\RequestHandler\CallbackRequestHandler;
use Syrma\WebContainer\RequestHandlerInterface;
use Syrma\WebContainer\ServerContext;
use Syrma\WebContainer\ServerContextInterface;
use Syrma\WebContainer\Tests\Exception\ExceptionHandlerStub;
use Syrma\WebContainer\Tests\Server\ServerNotAvaiableStub;
use Syrma\WebContainer\Tests\Server\ServerStub;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class ExecutorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotAvaiableServer()
    {
        $requestHandler = $this->getMock(RequestHandlerInterface::class);
        /* @var RequestHandlerInterface $requestHandler */
        $exceptionHandler = $this->getMock(ExceptionHandlerInterface::class);
        /* @var ExceptionHandlerInterface $exceptionHandler */

        new Executor(new ServerNotAvaiableStub(function () {}), $requestHandler, $exceptionHandler);
    }

    public function testExecute()
    {
        $startFn = function (ServerContextInterface $context, RequestHandlerInterface $requestHandler) {
            $request = $this->getMock(Requestinterface::class);
            /* @var RequestInterface $request */
            $response = $requestHandler->handle($request);
            $requestHandler->finish($request, $response);
        };

        $requestHandler = $this->getMock(RequestHandlerInterface::class);
        $requestHandler->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(RequestInterface::class))
            ->willReturn($this->getMock(ResponseInterface::class))
        ;
        $requestHandler->expects($this->once())
            ->method('finish')
            ->with(
                $this->isInstanceOf(RequestInterface::class),
                $this->isInstanceOf(ResponseInterface::class)
            )
        ;
        /* @var RequestHandlerInterface $requestHandler */
        $exceptionHandler = $this->getMock(ExceptionHandlerInterface::class);
        /* @var ExceptionHandlerInterface $exceptionHandler */

        $executor = new Executor(
            new ServerStub($startFn),
            $requestHandler,
            $exceptionHandler
        );
        $executor->execute(new ServerContext());
    }

    /**
     * @dataProvider provideExecuteWithContext
     *
     * @param string                 $address
     * @param int                    $port
     * @param ServerContextInterface $context
     */
    public function testExecuteWithContext($address, $port, ServerContextInterface $context = null)
    {
        $server = new ServerStub(function (ServerContextInterface $context) use ($address, $port) {
            $this->assertSame($address, $context->getListenAddress());
            $this->assertSame($port, $context->getListenPort());
        });

        $requestHandler = $this->getMock(RequestHandlerInterface::class);
        /* @var RequestHandlerInterface $requestHandler */
        $exceptionHandler = $this->getMock(ExceptionHandlerInterface::class);
        /* @var ExceptionHandlerInterface $exceptionHandler */

        $executor = new Executor($server, $requestHandler, $exceptionHandler);
        $executor->execute($context);
    }

    /**
     * @return array
     */
    public function provideExecuteWithContext()
    {
        return array(
            array(ServerContextInterface::DEFAULT_ADDRESS, ServerContextInterface::DEFAULT_PORT, new ServerContext()),
            array('127.0.0.1', 80, new ServerContext('127.0.0.1', 80)),
            array(ServerContextInterface::DEFAULT_ADDRESS, ServerContextInterface::DEFAULT_PORT, null),
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionCode 99
     */
    public function testExecuteWithException()
    {
        $startFn = function (ServerContextInterface $context, RequestHandlerInterface $requestHandler) {
            $request = $this->getMock(Requestinterface::class);
            /* @var RequestInterface $request */
            $response = $requestHandler->handle($request);
            $requestHandler->finish($request, $response);
        };

        $requestHandler = new CallbackRequestHandler(function () {
            throw new \RuntimeException('FooBar', 99);
        });

        $executor = new Executor(
            new ServerStub($startFn),
            $requestHandler,
            new ExceptionHandlerStub()
        );
        $executor->execute(new ServerContext());
    }
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionCode 99
     */
    public function testExecuteWithExceptionOnFinish()
    {
        $startFn = function (ServerContextInterface $context, RequestHandlerInterface $requestHandler) {
            $request = $this->getMock(Requestinterface::class);
            /* @var RequestInterface $request */
            $response = $requestHandler->handle($request);
            $requestHandler->finish($request, $response);
        };

        $requestHandler = new CallbackRequestHandler(function () {
            return new Response();
        }, function () {
            throw new \RuntimeException('FooBar', 99);
        });

        $executor = new Executor(
            new ServerStub($startFn),
            $requestHandler,
            new ExceptionHandlerStub()
        );
        $executor->execute(new ServerContext());
    }

    public function testExecuteWithInternalException()
    {
        $startFn = function (ServerContextInterface $context, RequestHandlerInterface $requestHandler) {
            $request = $this->getMock(Requestinterface::class);
            /* @var RequestInterface $request */
            $response = $requestHandler->handle($request);
            $this->assertEquals('TestContent', (string) $response->getBody());
        };

        $stopCounter = 0;
        $stopFn = function () use (&$stopCounter) {
            ++$stopCounter;
        };

        $requestHandler = new CallbackRequestHandler(function () {
            $stream = new Stream('php://temp', 'r+');
            $stream->write('TestContent');

            throw new ServerStopWithResponseException(new Response($stream), 'FooBar');
        });

        $executor = new Executor(
            new ServerStub($startFn, $stopFn),
            $requestHandler,
            new ExceptionHandlerStub()
        );
        $executor->execute(new ServerContext());

        $this->assertEquals(1, $stopCounter);
    }

    public function testExecuteWithExceptionAndInternalException()
    {
        $startFn = function (ServerContextInterface $context, RequestHandlerInterface $requestHandler) {
            $request = $this->getMock(Requestinterface::class);
            /* @var RequestInterface $request */
            $response = $requestHandler->handle($request);
            $this->assertEquals('TestContent', (string) $response->getBody());
        };

        $stopCounter = 0;
        $stopFn = function () use (&$stopCounter) {
            ++$stopCounter;
        };

        $requestHandler = new CallbackRequestHandler(function () {
            $stream = new Stream('php://temp', 'r+');
            $stream->write('TestContent');

            throw new \RuntimeException('', 0, new ServerStopWithResponseException(new Response($stream), 'FooBar'));
        });

        $executor = new Executor(
            new ServerStub($startFn, $stopFn),
            $requestHandler,
            new ExceptionHandlerStub()
        );
        $executor->execute(new ServerContext());

        $this->assertEquals(1, $stopCounter);
    }
}

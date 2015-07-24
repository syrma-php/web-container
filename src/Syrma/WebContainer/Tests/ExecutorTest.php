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
        new Executor(
            new ServerNotAvaiableStub(function () {}),
            $this->getMock(RequestHandlerInterface::class),
            $this->getMock(ExceptionHandlerInterface::class)
        );
    }

    public function testExecute()
    {
        $startFn = function (ServerContextInterface $context, RequestHandlerInterface $requestHandler) {
            $request = $this->getMock(Requestinterface::class);
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

        $executor = new Executor(
            new ServerStub($startFn),
            $requestHandler,
            $this->getMock(ExceptionHandlerInterface::class)
        );
        $executor->execute(new ServerContext());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionCode 99
     */
    public function testExecuteWithException()
    {
        $startFn = function (ServerContextInterface $context, RequestHandlerInterface $requestHandler) {
            $request = $this->getMock(Requestinterface::class);
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
            $response = $requestHandler->handle($this->getMock(Requestinterface::class));
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
            $response = $requestHandler->handle($this->getMock(Requestinterface::class));
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

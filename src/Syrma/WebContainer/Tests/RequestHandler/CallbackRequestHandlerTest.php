<?php

namespace Syrma\WebContainer\Tests\RequestHandler;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Syrma\WebContainer\RequestHandler\CallbackRequestHandler;

class CallbackRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return RequestInterface
     */
    private function getRequest()
    {
        return $this->getMock(RequestInterface::class);
    }

    /**
     * @return ResponseInterface
     */
    private function getResponse()
    {
        return $this->getMock(ResponseInterface::class);
    }

    public function testHandleWithClosure()
    {
        $handler = new CallbackRequestHandler(function ($request) {
            $this->assertInstanceOf(RequestInterface::class, $request);

            return $this->getResponse();
        }, function ($request, $response) {
            $this->assertInstanceOf(RequestInterface::class, $request);
            $this->assertInstanceOf(ResponseInterface::class, $response);
        });

        $result = $handler->handle($this->getRequest());
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testHandleWithCallable()
    {
        $mock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(array('handle', 'finish'))
            ->getMock()
        ;

        $mock->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(RequestInterface::class))
        ;

        $mock->expects($this->once())
            ->method('finish')
            ->with(
                $this->isInstanceOf(RequestInterface::class),
                $this->isInstanceOf(ResponseInterface::class)
            )
        ;

        $handler = new CallbackRequestHandler(
            array($mock, 'handle'),
            array($mock, 'finish')
        );
        $handler->handle($this->getRequest());
        $handler->finish($this->getRequest(), $this->getResponse());
    }
}

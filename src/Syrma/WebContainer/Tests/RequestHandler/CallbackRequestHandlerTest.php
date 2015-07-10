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
        $handler = new CallbackRequestHandler(function ($requset) {
            $this->assertInstanceOf(RequestInterface::class, $requset);

            return $this->getResponse();
        });

        $result = $handler->handle($this->getRequest());
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testHandleWithCallable()
    {
        $handler = new CallbackRequestHandler(array($this, 'handleMethod'));

        $result = $handler->handle($this->getRequest());
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function handleMethod($requset)
    {
        $this->assertInstanceOf(RequestInterface::class, $requset);

        return $this->getResponse();
    }
}

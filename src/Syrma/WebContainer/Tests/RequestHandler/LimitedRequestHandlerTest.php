<?php

namespace Syrma\WebContainer\Tests\RequestHandler;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Syrma\WebContainer\RequestHandler\Limitation\LimitationCheckerInterface;
use Syrma\WebContainer\RequestHandler\LimitedRequestHandler;
use Syrma\WebContainer\RequestHandlerInterface;

class LimitedRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleAndFinish()
    {
        $innerHandler = $this->getMock(RequestHandlerInterface::class);
        $innerHandler
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(RequestInterface::class))
        ;

        $innerHandler
            ->expects($this->once())
            ->method('finish')
            ->with($this->isInstanceOf(RequestInterface::class), $this->isInstanceOf(ResponseInterface::class))
        ;

        $checker1 = $this->getMock(LimitationCheckerInterface::class);
        $checker1
            ->expects($this->once())
            ->method('checkLimit')
        ;

        $checker2 = $this->getMock(LimitationCheckerInterface::class);
        $checker2
            ->expects($this->once())
            ->method('checkLimit')
        ;

        $checker3 = $this->getMock(LimitationCheckerInterface::class);
        $checker3
            ->expects($this->once())
            ->method('checkLimit')
        ;

        /* @var RequestHandlerInterface $innerHandler */
        $requestHandler = new LimitedRequestHandler($innerHandler, array($checker1, $checker2));
        /* @var LimitationCheckerInterface $checker3 */
        $requestHandler->addChecker($checker3);

        /** @var RequestInterface $request */
        $request = $this->getMock(RequestInterface::class);
        /** @var ResponseInterface $response */
        $response = $this->getMock(ResponseInterface::class);

        $requestHandler->handle($request);
        $requestHandler->finish($request, $response);
    }
}

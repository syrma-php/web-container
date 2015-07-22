<?php

namespace Syrma\WebContainer\Tests\RequestHandler;

use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Syrma\WebContainer\RequestHandler\SymfonyRequestHandler;
use Syrma\WebContainer\Tests\RequestHandler\fixtures\TestTerminableHttpKernelInterface;
use Zend\Diactoros\ServerRequest;

class SymfonyRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected function createHandler()
    {
        $kernel = $this->getMock(TestTerminableHttpKernelInterface::class);

        $kernel->expects($this->once())
            ->method('handle')
            ->willReturnCallback(function (Request $request) {

                return new Response(
                    'Symfony response for:'.$request->getRequestUri(),
                    201,
                    array(
                        'X-Debug' => 'qwerty',
                        'Date' => '1970-01-02 10:11:12',
                    )
                );
            })
        ;

        $kernel->expects($this->once())
            ->method('terminate')
            ->with(
                $this->isInstanceOf(Request::class),
                $this->isInstanceOf(Response::class)
            )
        ;

        /* @var $kernel  HttpKernelInterface */
        return new SymfonyRequestHandler($kernel, new HttpFoundationFactory(), new DiactorosFactory());
    }

    public function testHandle()
    {
        $handler = $this->createHandler();

        $request = new ServerRequest(array(
            'HOST' => 'http://syrma.local',
            'REQUEST_URI' => '/foo/bar',
        ));

        $this->assertEquals(0, $handler->getRequestMappingCount());

        $response = $handler->handle($request);

        $this->assertEquals(1, $handler->getRequestMappingCount());

        $handler->finish($request, $response);

        $this->assertEquals(0, $handler->getRequestMappingCount());

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('Symfony response for:/foo/bar', (string) $response->getBody());
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(array(
            'x-debug' => array('qwerty'),
            'cache-control' => array('no-cache'),
            'date' => array('1970-01-02 10:11:12'),
        ), $response->getHeaders());
    }
}

<?php

namespace Syrma\WebContainer\Tests\Exception;

use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Syrma\WebContainer\Exception\DefaultExceptionHandler;
use Syrma\WebContainer\Exception\HttpException;
use Syrma\WebContainer\Exception\ServerStopWithResponseException;
use Syrma\WebContainer\Util\ErrorPageLoader;
use Syrma\WebContainer\Util\ZendPsr7Factory;

class DefaultExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected function createHandler()
    {
        return new DefaultExceptionHandler(
            new ZendPsr7Factory(),
            ErrorPageLoader::createDefault()
        );
    }

    /**
     * @expectedException \Syrma\WebContainer\Exception\ServerStopWithResponseException
     * @expectedExceptionCode 99
     */
    public function testHandleWithLogger()
    {
        $handler = $this->createHandler();

        $logger = $this->getMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('critical')
            ->with(
                $this->stringContains('FooBar'),
                $this->arrayHasKey('exception')
            )
        ;

        $handler->setLogger($logger);
        $handler->handle(new \Exception('FooBar', 99));
    }

    public function testHandleWithoutLogger()
    {
        $log = tmpfile();
        $logFile = stream_get_meta_data($log)['uri'];
        $this->iniSet('error_log', $logFile);

        $handler = $this->createHandler();
        try {
            $handler->handle(new \Exception('FooBar', 88));
            $this->fail('No throw exception');
        } catch (ServerStopWithResponseException $ex) {
            $this->assertContains('FooBar', $ex->getMessage());
            $this->assertEquals(88, $ex->getCode());
        }

        fseek($log, 0);
        $logContent = stream_get_contents($log, 256);
        fclose($log);

        $this->assertContains('FooBar', $logContent);
        $this->assertContains('CRITICAL', $logContent);
    }

    public function testCreateErrorResponseByException()
    {
        $handler = $this->createHandler();
        $response = $handler->createErrorResponseByException(new \Exception('FooBar', 88));

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('<html>', $response->getBody()->getContents());
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertCount(2, $response->getHeaders());
        $this->assertEquals('text/html; charset=UTF-8', $response->getHeaderLine('Content-Type'));
        $this->assertEquals('max-age=0,must-revalidate,no-cache,no-store,private', $response->getHeaderLine('Cache-Control'));
    }

    public function testCreateErrorResponseByExceptionWithHttpException()
    {
        $ex = new HttpException(513, array(
            'X-Foo' => array('bar'),
        ));

        $handler = $this->createHandler();
        $response = $handler->createErrorResponseByException($ex);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('<html>', $response->getBody()->getContents());
        $this->assertEquals(513, $response->getStatusCode());
        $this->assertCount(1, $response->getHeaders());
        $this->assertEquals('bar', $response->getHeaderLine('X-Foo'));
    }
}

<?php

namespace Syrma\WebContainer\Tests\Server\Swoole;

use Syrma\WebContainer\Server\Swoole\SwooleMessageTransformer;
use Syrma\WebContainer\Util\ZendPsr7Factory;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;

class SwooleMessageTransformerResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return SwooleMessageTransformer
     */
    protected function createTransformer()
    {
        return new SwooleMessageTransformer(new ZendPsr7Factory());
    }

    protected function createSwooleResponse(\ArrayObject $dataStore)
    {
        $resp = $this->getMock(\swoole_http_response::class);

        $resp->expects($this->any())
            ->method('header')
            ->willReturnCallback(function ($name, $value) use ($dataStore) {
                $dataStore['headers'][$name] = $value;
            })
        ;

        $resp->expects($this->any())
            ->method('write')
            ->willReturnCallback(function ($value) use ($dataStore) {
                $dataStore['content'] .= $value;
            })
        ;

        $resp->expects($this->once())
            ->method('status')
            ->willReturnCallback(function ($value) use ($dataStore) {
                $dataStore['status'] = $value;
            })
        ;

        $resp->expects($this->once())
            ->method('end')
            ->willReturn(null)
        ;

        return $resp;
    }

    protected function createDataStore()
    {
        return new \ArrayObject(array(
            'content' => null,
            'status' => null,
            'headers' => array(),
        ));
    }

    public function testHtmlReponse()
    {
        $dataStore = $this->createDataStore();
        $response = new HtmlResponse('<strong>Hello World!</strong>', 200, array('X-Foo' => 'bar'));
        $swooleResponse = $this->createSwooleResponse($dataStore);

        $this->createTransformer()->reverseTransform($response, $swooleResponse);

        $this->assertEquals(200, $dataStore['status']);
        $this->assertEquals('<strong>Hello World!</strong>', $dataStore['content']);
        $this->assertEquals(array(
            'X-Foo' => 'bar',
            'content-type' => 'text/html',
        ), $dataStore['headers']);
    }

    public function testReridectResponse()
    {
        $dataStore = $this->createDataStore();
        $response = new RedirectResponse('/foo/bar');
        $swooleResponse = $this->createSwooleResponse($dataStore);

        $this->createTransformer()->reverseTransform($response, $swooleResponse);

        $this->assertEquals(302, $dataStore['status']);
        $this->assertEquals('', $dataStore['content']);
        $this->assertEquals(array(
            'location' => '/foo/bar',
        ), $dataStore['headers']);
    }

    public function testEmptyResponse()
    {
        $dataStore = $this->createDataStore();
        $response = new EmptyResponse();
        $swooleResponse = $this->createSwooleResponse($dataStore);

        $this->createTransformer()->reverseTransform($response, $swooleResponse);

        $this->assertEquals(204, $dataStore['status']);
        $this->assertEquals('', $dataStore['content']);
        $this->assertEquals(array(), $dataStore['headers']);
    }

    public function testJsonResponse()
    {
        $dataStore = $this->createDataStore();
        $response = new JsonResponse(array('foo' => 'bar'), 201);
        $swooleResponse = $this->createSwooleResponse($dataStore);

        $this->createTransformer()->reverseTransform($response, $swooleResponse);

        $this->assertEquals(201, $dataStore['status']);
        $this->assertEquals('{"foo":"bar"}', $dataStore['content']);
        $this->assertEquals(array(
            'content-type' => 'application/json',
        ), $dataStore['headers']);
    }
}

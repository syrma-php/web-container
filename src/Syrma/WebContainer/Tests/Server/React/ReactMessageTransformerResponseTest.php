<?php

namespace Syrma\WebContainer\Tests\Server\React;

use React\Http\Response;
use Syrma\WebContainer\Server\React\ReactMessageTransformer;
use Syrma\WebContainer\Util\ZendPsr7Factory;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;

class ReactMessageTransformerResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return ReactMessageTransformer
     */
    protected function createTransformer()
    {
        return new ReactMessageTransformer(new ZendPsr7Factory());
    }

    /**
     * @return Response
     */
    protected function createReactResponse(\ArrayObject $dataStore)
    {
        $mock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mock->expects($this->once())
            ->method('writeHead')
            ->willReturnCallback(function ($status, array $headers) use ($dataStore) {
                $dataStore['status'] = $status;
                $dataStore['headers'] = $headers;
            })
        ;

        $mock->expects($this->any())
            ->method('write')
            ->willReturnCallback(function ($value) use ($dataStore) {
                $dataStore['content'] .= $value;
            })
        ;

        $mock->expects($this->once())
            ->method('end')
        ;

        return $mock;
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
        $reactResponse = $this->createReactResponse($dataStore);

        $this->createTransformer()->reverseTransform($response, $reactResponse);

        $this->assertEquals(200, $dataStore['status']);
        $this->assertEquals('<strong>Hello World!</strong>', $dataStore['content']);
        $this->assertEquals(array(
            'X-Foo' => 'bar',
            'content-type' => 'text/html',
            'Content-Length' => 29,
        ), $dataStore['headers']);
    }

    public function testReridectResponse()
    {
        $dataStore = $this->createDataStore();
        $response = new RedirectResponse('/foo/bar');
        $reactResponse = $this->createReactResponse($dataStore);

        $this->createTransformer()->reverseTransform($response, $reactResponse);

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
        $reactResponse = $this->createReactResponse($dataStore);

        $this->createTransformer()->reverseTransform($response, $reactResponse);

        $this->assertEquals(204, $dataStore['status']);
        $this->assertEquals('', $dataStore['content']);
        $this->assertEquals(array(), $dataStore['headers']);
    }

    public function testJsonResponse()
    {
        $dataStore = $this->createDataStore();
        $response = new JsonResponse(array('foo' => 'bar'), 201);
        $reactResponse = $this->createReactResponse($dataStore);

        $this->createTransformer()->reverseTransform($response, $reactResponse);

        $this->assertEquals(201, $dataStore['status']);
        $this->assertEquals('{"foo":"bar"}', $dataStore['content']);
        $this->assertEquals(array(
            'content-type' => 'application/json',
            'Content-Length' => 13,
        ), $dataStore['headers']);
    }
}

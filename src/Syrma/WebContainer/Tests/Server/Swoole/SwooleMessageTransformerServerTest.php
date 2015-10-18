<?php

namespace Syrma\WebContainer\Tests\Server\Swoole;

use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;

class SwooleMessageTransformerServerTest  extends AbstractSwooleMessageTransformerTest
{
    /**
     * {@inheritdoc}
     */
    protected function isUseServerRequest()
    {
        return true;
    }

    public function testServerParams()
    {
        $swooleRequest = $this->createSwooleRequest(array(
            'request_method' => 'GET',
            'request_uri' => '/',
            'path_info' => '/',
            'request_time' => 123456789,
            'server_port' => 9100,
            'remote_port' => 49648,
            'remote_addr' => '127.0.0.1',
            'server_protocol' => 'HTTP/1.1',
            'server_software' => 'swoole-http-server',
        ), array(
            'authorization' => 'Basic dXNlcm5hbWU6cGFzc3dvcmQ=',
            'host' => 'syrma.local',
            'connection' => 'close',
            'user-agent' => 'curl/7.35.0',
            'accept' => '*/*',
        ));

        $request = $this->createTransformer()->transform($swooleRequest);
        $expServerParams = new ServerRequest(ServerRequestFactory::normalizeServer(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
            'PATH_INFO' => '/',
            'REQUEST_TIME' => 123456789,
            'SERVER_PORT' => 9100,
            'REMOTE_PORT' => 49648,
            'REMOTE_ADDR' => '127.0.0.1',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'SERVER_SOFTWARE' => 'swoole-http-server',

            'HTTP_AUTHORIZATION' => 'Basic dXNlcm5hbWU6cGFzc3dvcmQ=',
            'HTTP_HOST' => 'syrma.local',
            'HTTP_CONNECTION' => 'close',
            'HTTP_USER-AGENT' => 'curl/7.35.0',
            'HTTP_ACCEPT' => '*/*',
        )));

        $this->assertInstanceOf(ServerRequest::class, $request);
        /* @var  ServerRequest $request */
        $this->assertSame($expServerParams->getServerParams(), $request->getServerParams());
    }

    public function testUpladedFiles()
    {
        $rawFiles = array(
            'file1' => array(
                'name' => 'space-05.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => '/tmp/2f588692b23b25fdd88f9ad4848dbd17',
                'error' => 0,
                'size' => 14620,
            ),
            'myFiles' => array(
                array(
                    'name' => 'space-04.jpg',
                    'type' => 'image/png',
                    'tmp_name' => '/tmp/2794797237924797237d88f9ad4848dbd17',
                    'error' => 0,
                    'size' => 1620,
                ),
                array(
                    'name' => 'space-03.jpg',
                    'type' => 'image/png',
                    'tmp_name' => '/tmp/2794797237924797237d88f9ad4848d1237',
                    'error' => 0,
                    'size' => 620,
                ),
            ),
        );

        $swooleRequest = $this->createSwooleRequest(array(), array(), $rawFiles);
        $request = $this->createTransformer()->transform($swooleRequest);
        $this->assertInstanceOf(ServerRequest::class, $request);
        $files = ServerRequestFactory::normalizeFiles($rawFiles);

        /* @var  ServerRequest $request */
        $this->assertEquals($files, $request->getUploadedFiles());
    }

    public function testCookie()
    {
        $swooleRequest = $this->createSwooleRequest(
            array(),
            array(),
            array(),
            null,
            array(
                'foo' => 'fooVal',
                'bar' => 'barVal',
            )
        );

        $request = $this->createTransformer()->transform($swooleRequest);
        $this->assertInstanceOf(ServerRequest::class, $request);

        /* @var  ServerRequest $request */
        $this->assertEquals(array(
            'foo' => 'fooVal',
            'bar' => 'barVal',
        ), $request->getCookieParams());
    }

    public function testPost()
    {
        $swooleRequest = $this->createSwooleRequest();
        $swooleRequest->post = array(
            'foo' => '&bar',
            'hello' => 'world!',
        );

        $request = $this->createTransformer()->transform($swooleRequest);
        $this->assertInstanceOf(ServerRequest::class, $request);

        /* @var  ServerRequest $request */
        $this->assertEquals(array(
            'foo' => '&bar',
            'hello' => 'world!',
        ), $request->getParsedBody());
    }
}

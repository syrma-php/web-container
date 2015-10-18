<?php

namespace Syrma\WebContainer\Tests\Server\React;

use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;

class ReactMessageTransformerServerTest extends AbstractReactMessageTransformerTest
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
        $reactRequest = $this->createReactRequest('GET', '/', array(), '1.1', array(
            'Host' => 'syrma.local',
            'Connection' => 'close',
            'User-Agent' => 'curl/7.35.0',
            'Accept' => '*/*',
            'Authorization' => 'Basic dXNlcm5hbWU6cGFzc3dvcmQ=',
        ));
        $reactRequest->remoteAddress = '127.0.0.1';

        $request = $this->createTransformer()->transform($reactRequest);
        $expServerParams = new ServerRequest(ServerRequestFactory::normalizeServer(array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'SERVER_SOFTWARE' => 'reactphp-http',
            'REMOTE_ADDR' => '127.0.0.1',

            'HTTP_AUTHORIZATION' => 'Basic dXNlcm5hbWU6cGFzc3dvcmQ=',
            'HTTP_HOST' => 'syrma.local',
            'HTTP_CONNECTION' => 'close',
            'HTTP_USER-AGENT' => 'curl/7.35.0',
            'HTTP_ACCEPT' => '*/*',
        )));

        $this->assertInstanceOf(ServerRequest::class, $request);
        /* @var  ServerRequest $request */
        $this->assertEquals($expServerParams->getServerParams(), $request->getServerParams());
    }

    public function testUpladedFiles()
    {
        $f1 = fopen('php://memory', 'r');
        $f2 = fopen('php://memory', 'r');
        $f3 = fopen('php://memory', 'r');

        $rawFiles = array(
            'file1' => array(
                'name' => 'space-05.jpg',
                'type' => 'image/jpeg',
                'stream' => $f1,
                'error' => 0,
                'size' => 14620,
            ),
            'myFiles' => array(
                array(
                    'name' => 'space-04.jpg',
                    'type' => 'image/png',
                    'stream' => $f2,
                    'error' => 0,
                    'size' => 1620,
                ),
                array(
                    'name' => 'space-03.jpg',
                    'type' => 'image/png',
                    'stream' => $f3,
                    'error' => 0,
                    'size' => 620,
                ),

            ),
        );
        $refFiles = array(
            'file1' => array(
                'name' => 'space-05.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => $f1,
                'error' => 0,
                'size' => 14620,
            ),
            'myFiles' => array(
                array(
                    'name' => 'space-04.jpg',
                    'type' => 'image/png',
                    'tmp_name' => $f2,
                    'error' => 0,
                    'size' => 1620,
                ),
                array(
                    'name' => 'space-03.jpg',
                    'type' => 'image/png',
                    'tmp_name' => $f3,
                    'error' => 0,
                    'size' => 620,
                ),
            ),
        );

        try {
            $reactRequest = $this->createReactRequest('POST');
            $reactRequest->setFiles($rawFiles);

            $request = $this->createTransformer()->transform($reactRequest);
            $this->assertInstanceOf(ServerRequest::class, $request);
            $files = ServerRequestFactory::normalizeFiles($refFiles);

            /* @var  ServerRequest $request */
            $this->assertEquals($files, $request->getUploadedFiles());
        } finally {
            fclose($f1);
            fclose($f2);
            fclose($f3);
        }
    }

    public function testCookie()
    {
        $reactRequest = $this->createReactRequest('GET', '/', array(), '1.1', array(
            'Cookie' => 'theme=light; sessionToken=abc123',
        ));

        $request = $this->createTransformer()->transform($reactRequest);
        $this->assertInstanceOf(ServerRequest::class, $request);

        /* @var  ServerRequest $request */
        $this->assertEquals(array(
            'theme' => 'light',
            'sessionToken' => 'abc123',
        ), $request->getCookieParams());
    }

    public function testPost()
    {
        $reactRequest = $this->createReactRequest('POST');
        $reactRequest->setPost(array(
            'foo' => '&bar',
            'hello' => 'world!',
        ));

        $request = $this->createTransformer()->transform($reactRequest);
        $this->assertInstanceOf(ServerRequest::class, $request);

        /* @var  ServerRequest $request */
        $this->assertEquals(array(
            'foo' => '&bar',
            'hello' => 'world!',
        ), $request->getParsedBody());
    }
}

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
        $this->markTestSkipped('The multipart request is not supported in ReactHttpServer');
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
}

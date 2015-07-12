<?php

namespace Syrma\WebContainer\Tests\Server\Swoole;

use Syrma\WebContainer\Server\Swoole\SwooleMessageTransformer;
use Syrma\WebContainer\Util\ZendPsr7Factory;
use Zend\Diactoros\Uri;

abstract class AbstractSwooleMessageTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return bool
     */
    abstract protected function isUseServerRequest();

    /**
     * @param array       $server
     * @param array       $header
     * @param array       $files
     * @param string|null $rawContnet
     * @param array       $cookie
     *
     * @return \swoole_http_request
     */
    protected function createSwooleRequest(
        array $server = array(),
        array $header = array(),
        array $files = null,
        $rawContnet = null,
        array $cookie = null

    ) {
        $swooleRequest = $this->getMock(\swoole_http_request::class);

        $swooleRequest->server = $server;
        $swooleRequest->header = $header;
        $swooleRequest->files = $files;
        $swooleRequest->cookie = $cookie;

        if (null !== $rawContnet) {
            $swooleRequest->fd = 1; //fake value
            $swooleRequest->method('rawContent')
                ->willReturn($rawContnet)
            ;
        }

        return $swooleRequest;
    }

    /**
     * @return SwooleMessageTransformer
     */
    protected function createTransformer()
    {
        return new SwooleMessageTransformer(new ZendPsr7Factory(), $this->isUseServerRequest());
    }

    public function testMinimalUri()
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
            'connection' => 'close',
            'user-agent' => 'curl/7.35.0',
            'accept' => '*/*',
        ));

        $request = $this->createTransformer()->transform($swooleRequest);
        $expUri = new Uri('http://localhost/');
        $this->assertEquals($expUri, $request->getUri());
    }

    public function testSimpleUri()
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
            'host' => 'syrma.local',
            'connection' => 'close',
            'user-agent' => 'curl/7.35.0',
            'accept' => '*/*',
        ));

        $request = $this->createTransformer()->transform($swooleRequest);
        $expUri = new Uri('http://syrma.local/');
        $this->assertEquals($expUri, $request->getUri());
    }

    public function testSimpleUriHttps()
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
            'https' => 'on',
        ), array(
            'host' => 'syrma.local',
            'connection' => 'close',
            'user-agent' => 'curl/7.35.0',
            'accept' => '*/*',
        ));

        $request = $this->createTransformer()->transform($swooleRequest);
        $expUri = new Uri('https://syrma.local/');
        $this->assertEquals($expUri, $request->getUri());
    }

    public function testSimpleUriWithProxyParam()
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
            'host' => 'syrma.local',
            'x-real-ip' => '127.0.0.1',
            'x-forwarded-for' => '127.0.0.1',
            'x-forwarded-proto' => 'https',
            'x-forwarded-host' => 'syrma.local:560',
            'connection' => 'close',
            'user-agent' => 'curl/7.35.0',
            'accept' => '*/*',
        ));

        $request = $this->createTransformer()->transform($swooleRequest);

        $expUri = new Uri('https://syrma.local:560/');
        $this->assertEquals($expUri, $request->getUri());
    }

    public function testUriWithPathAndQuery()
    {
        $swooleRequest = $this->createSwooleRequest(array(
            'request_method' => 'GET',
            'query_string' => 'foo=1&bar[]=2&bar[]=8',
            'request_uri' => '/thePath/theSub-path/',
            'path_info' => '/thePath/theSub-path/',
            'request_time' => 123456789,
            'server_port' => 9100,
            'remote_port' => 49648,
            'remote_addr' => '127.0.0.1',
            'server_protocol' => 'HTTP/1.1',
            'server_software' => 'swoole-http-server',
        ), array(
            'host' => 'syrma.local',
            'connection' => 'close',
            'user-agent' => 'curl/7.35.0',
            'accept' => '*/*',
        ));

        $request = $this->createTransformer()->transform($swooleRequest);
        $expUri = new Uri('http://syrma.local/thePath/theSub-path/?foo=1&bar[]=2&bar[]=8');
        $this->assertEquals($expUri, $request->getUri());
    }

    public function testRequestMethod()
    {
        $swooleRequest = $this->createSwooleRequest(array(
            'request_method' => 'GET',
        ));
        $request = $this->createTransformer()->transform($swooleRequest);
        $this->assertEquals('GET', $request->getMethod());

        $swooleRequestPost = $this->createSwooleRequest(array(
            'request_method' => 'POST',
        ));
        $requestPost = $this->createTransformer()->transform($swooleRequestPost);
        $this->assertEquals('POST', $requestPost->getMethod());
    }

    public function testProtocolVersion()
    {
        $swooleRequest = $this->createSwooleRequest(array('server_protocol' => 'HTTP/1.1'));
        $request = $this->createTransformer()->transform($swooleRequest);
        $this->assertEquals('1.1', $request->getProtocolVersion());

        $swooleRequest = $this->createSwooleRequest(array('server_protocol' => 'HTTP/1.0'));
        $request = $this->createTransformer()->transform($swooleRequest);
        $this->assertEquals('1.0', $request->getProtocolVersion());

        $swooleRequest = $this->createSwooleRequest(array('server_protocol' => 'HTTP/a.b'));
        $request = $this->createTransformer()->transform($swooleRequest);
        $this->assertEquals('1.1', $request->getProtocolVersion());
    }

    public function testBody()
    {
        $swooleRequest = $this->createSwooleRequest(
            array(),
            array(),
            array(),
            'Hello World!'
        );

        $request = $this->createTransformer()->transform($swooleRequest);
        $this->assertEquals('Hello World!', (string) $request->getBody());
    }

    public function testHeader()
    {
        $swooleRequest = $this->createSwooleRequest(array(), array(
            'host' => 'syrma.local',
            'connection' => 'close',
            'user-agent' => 'curl/7.35.0',
            'accept' => '*/*',
            'cache-control' => 'max-age=0',
        ));

        $request = $this->createTransformer()->transform($swooleRequest);

        $expHeaders = array(
            'Host' => array('syrma.local'),
            'Connection' => array('close'),
            'User-Agent' => array('curl/7.35.0'),
            'Accept' => array('*/*'),
            'Cache-Control' => array('max-age=0'),
        );

        $this->assertEquals($expHeaders, $request->getHeaders());
    }
}

<?php

namespace Syrma\WebContainer\Tests\Server\React;

use React\Http\Request as ReactRequest;
use Syrma\WebContainer\Server\React\ReactMessageTransformer;
use Syrma\WebContainer\Util\ZendPsr7Factory;
use Zend\Diactoros\Uri;

abstract class AbstractReactMessageTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return bool
     */
    abstract protected function isUseServerRequest();

    /**
     * @param $method
     * @param string $path
     * @param array  $query
     * @param string $httpVersion
     * @param array  $headers
     *
     * @return ReactRequest
     */
    protected function createReactRequest(
        $method,
        $path = '/',
        array $query = array(),
        $httpVersion = '1.1',
        array $headers = array()
    ) {
        return new ReactRequest($method, $path, $query, $httpVersion, $headers);
    }

    /**
     * @return ReactMessageTransformer
     */
    protected function createTransformer()
    {
        return new ReactMessageTransformer(new ZendPsr7Factory(), $this->isUseServerRequest());
    }

    public function testMinimalUri()
    {
        $reactRequest = $this->createReactRequest('GET');

        $request = $this->createTransformer()->transform($reactRequest);
        $expUri = new Uri('http://localhost/');
        $this->assertEquals($expUri, $request->getUri());
    }

    public function testSimpleUri()
    {
        $reactRequest = $this->createReactRequest(
            'GET',
            '/',
            array(),
            '1.1',
            array(
                'Host' => 'syrma.local',
            )
        );

        $request = $this->createTransformer()->transform($reactRequest);
        $expUri = new Uri('http://syrma.local/');
        $this->assertEquals($expUri, $request->getUri());
    }

    public function testSimpleUriHttps()
    {
        $reactRequest = $this->createReactRequest(
            'GET',
            '/',
            array(),
            '1.1',
            array(
                'Host' => 'syrma.local',
                'Https' => 'on',
            )
        );

        $request = $this->createTransformer()->transform($reactRequest);
        $expUri = new Uri('https://syrma.local/');
        $this->assertEquals($expUri, $request->getUri());
    }

    public function testSimpleUriWithProxyParam()
    {
        $reactRequest = $this->createReactRequest(
            'GET',
            '/',
            array(),
            '1.1',
            array(
                'Host' => 'syrma.local',
                'X-Forwarded-For' => '127.0.0.1',
                'X-Forwarded-Proto' => 'https',
                'X-Forwarded-Host' => 'syrma.local:560',
            )
        );

        $request = $this->createTransformer()->transform($reactRequest);

        $expUri = new Uri('https://syrma.local:560/');
        $this->assertEquals($expUri, $request->getUri());
    }

    public function testUriWithPathAndQuery()
    {
        $reactRequest = $this->createReactRequest(
            'GET',
            '/thePath/theSub-path/',
            array(
                'foo' => 1,
                'bar' => array(2, 8),
            ),
            '1.1',
            array(
                'Host' => 'syrma.local',
            )
        );

        $request = $this->createTransformer()->transform($reactRequest);
        $expUri = new Uri('http://syrma.local/thePath/theSub-path/?foo=1&bar[0]=2&bar[1]=8'); //TODO - numerical index
        $this->assertEquals($expUri, $request->getUri());
    }

    public function testRequestMethod()
    {
        $reactRequest = $this->createReactRequest('GET');

        $request = $this->createTransformer()->transform($reactRequest);
        $this->assertEquals('GET', $request->getMethod());

        $reactRequest = $this->createReactRequest('POST');

        $requestPost = $this->createTransformer()->transform($reactRequest);
        $this->assertEquals('POST', $requestPost->getMethod());
    }

    public function testProtocolVersion()
    {
        $reactRequest = $this->createReactRequest('GET', '/', array(), '1.1');
        $request = $this->createTransformer()->transform($reactRequest);
        $this->assertEquals('1.1', $request->getProtocolVersion());

        $reactRequest = $this->createReactRequest('GET', '/', array(), '1.0');
        $request = $this->createTransformer()->transform($reactRequest);
        $this->assertEquals('1.0', $request->getProtocolVersion());
    }

    public function testBody()
    {
        $this->markTestSkipped('The multipart request is not supported in ReactHttpServer');
    }

    public function testHeader()
    {
        $reactRequest = $this->createReactRequest('GET', '/', array(), '1.1', array(
            'Host' => 'syrma.local',
            'Connection' => 'close',
            'User-Agent' => 'curl/7.35.0',
            'Accept' => '*/*',
            'Cache-Control' => 'max-age=0',
        ));

        $request = $this->createTransformer()->transform($reactRequest);

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

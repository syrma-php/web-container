<?php

namespace Syrma\WebContainer\Tests\Util;

use Psr\Http\Message\StreamInterface;
use Syrma\WebContainer\Util\ZendPsr7Factory;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\UploadedFile;

class ZendPsr7FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ZendPsr7Factory
     */
    private $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = new ZendPsr7Factory();
    }

    public function testCreateRequest()
    {
        $body = fopen('php://temp', 'wb+');

        $reqFull = $this->factory->createRequest(
            '/foo', 'POST', $body, array('X-Foo' => 'fooBar')
        );
        $reqFullEx = new Request(
            '/foo', 'POST', $body, array('X-Foo' => 'fooBar')
        );

        try {
            $this->assertEquals($reqFullEx, $reqFull);
        } finally {
            fclose($body);
        }
    }

    public function testCreateServerRequest()
    {
        $body = fopen('php://temp', 'wb+');
        $file = fopen('php://temp', 'wb+');
        $uploadedFile = new UploadedFile($file, 10, 1, 'foo', 'image/png');

        $reqFull = $this->factory->createServerRequest(
            array('SERVER_NAME' => 'SyrmaWebContainer'),
            array($uploadedFile),
            '/foo', 'POST', $body, array('X-Foo' => 'fooBar')
        );
        $reqFullEx = new ServerRequest(
            array('SERVER_NAME' => 'SyrmaWebContainer'),
            array($uploadedFile),
            '/foo', 'POST', $body, array('X-Foo' => 'fooBar')
        );

        try {
            $this->assertEquals($reqFullEx, $reqFull);
        } finally {
            fclose($body);
            fclose($file);
        }
    }

    public function testCreateUploadedFile()
    {
        $file = fopen('php://temp', 'wb+');
        $fileFull = $this->factory->createUploadedFile(
            $file, 10, 1, 'foo', 'image/png'
        );
        $fileFullEx = new UploadedFile($file, 10, 1, 'foo', 'image/png');

        try {
            $this->assertEquals($fileFullEx, $fileFull);
        } finally {
            fclose($file);
        }
    }

    public function testCreateResponse()
    {
        $stream = fopen('php://temp', 'wb+');
        $headers = array(
            'Content-Type' => array('text/html; charset=UTF-8'),
        );

        $response = $this->factory->createResponse($stream, 301, $headers);
        $responseEx = new Response($stream, 301, $headers);

        try {
            $this->assertEquals($response, $responseEx);
        } finally {
            fclose($stream);
        }
    }

    public function testCreateStream()
    {
        $stream = $this->factory->createStream();
        $this->assertInstanceOf(StreamInterface::class, $stream);

        $stream->write('Hello World!');
        $stream->rewind();

        $this->assertSame('Hello World!', $stream->getContents());
    }
}

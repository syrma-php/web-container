<?php

namespace Syrma\WebContainer\Tests\Util;

use Syrma\WebContainer\Util\ZendPsr7Factory;
use Zend\Diactoros\Request;
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
}

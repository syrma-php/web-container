<?php

namespace Syrma\WebContainer\Tests\Exception;

use Syrma\WebContainer\Exception\HttpException;

class HttpExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testWithOneParams()
    {
        $ex = new HttpException(500);

        $this->assertSame(500, $ex->getStatusCode());
        $this->assertSame(array(), $ex->getHeaders());
        $this->assertSame('', $ex->getMessage());
        $this->assertSame(0, $ex->getCode());
        $this->assertNull($ex->getPrevious());
    }

    public function testWithAllParams()
    {
        $prevEx = new \RuntimeException();
        $headers = array(
            'X-Foo' => array('bar'),
        );

        $ex = new HttpException(503, $headers, 'foo', 42, $prevEx);

        $this->assertSame(503, $ex->getStatusCode());
        $this->assertSame($headers, $ex->getHeaders());
        $this->assertSame('foo', $ex->getMessage());
        $this->assertSame(42, $ex->getCode());
        $this->assertSame($prevEx, $ex->getPrevious());
    }
}

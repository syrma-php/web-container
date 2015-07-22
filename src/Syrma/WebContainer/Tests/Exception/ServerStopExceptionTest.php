<?php

namespace Syrma\WebContainer\Tests\Exception;

use Psr\Http\Message\ResponseInterface;
use Syrma\WebContainer\Exception\ServerStopException;

class ServerStopExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testWithOneParams()
    {
        $response = $this->getMock(ResponseInterface::class);
        $ex = new ServerStopException($response);

        $this->assertSame($response, $ex->getResponse());
        $this->assertSame('', $ex->getMessage());
        $this->assertSame(0, $ex->getCode());
        $this->assertNull($ex->getPrevious());
    }

    public function testWithAllParams()
    {
        $response = $this->getMock(ResponseInterface::class);
        $prevEx = new \RuntimeException();

        $ex = new ServerStopException($response, 'foo', 42, $prevEx);

        $this->assertSame($response, $ex->getResponse());
        $this->assertSame('foo', $ex->getMessage());
        $this->assertSame(42, $ex->getCode());
        $this->assertSame($prevEx, $ex->getPrevious());
    }
}

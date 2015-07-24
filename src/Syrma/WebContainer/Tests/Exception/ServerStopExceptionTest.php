<?php

namespace Syrma\WebContainer\Tests\Exception;

use Syrma\WebContainer\Exception\ServerStopException;

class ServerStopExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testWithOneParams()
    {
        $ex = new ServerStopException();

        $this->assertSame('', $ex->getMessage());
        $this->assertSame(0, $ex->getCode());
        $this->assertNull($ex->getPrevious());
    }

    public function testWithAllParams()
    {
        $prevEx = new \RuntimeException();

        $ex = new ServerStopException('foo', 42, $prevEx);

        $this->assertSame('foo', $ex->getMessage());
        $this->assertSame(42, $ex->getCode());
        $this->assertSame($prevEx, $ex->getPrevious());
    }
}

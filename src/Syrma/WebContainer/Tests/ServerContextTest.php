<?php


namespace Syrma\WebContainer\Tests;


use Syrma\WebContainer\ServerContext;
use Syrma\WebContainer\ServerContextInterface;

class ServerContextTest extends \PHPUnit_Framework_TestCase {

    public function testDefaultValue(){

        $context = new ServerContext();
        $this->assertSame(ServerContextInterface::DEFAULT_ADDRESS, $context->getListenAddress());
        $this->assertSame(ServerContextInterface::DEFAULT_PORT, $context->getListenPort());
    }

    public function testCustomValue(){

        $context = new ServerContext( '1.1.1.1', 8080 );
        $this->assertSame('1.1.1.1', $context->getListenAddress());
        $this->assertSame(8080, $context->getListenPort());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionCode 1
     */
    public function testEmptyAddress(){
        $context = new ServerContext( NULL );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionCode 3
     */
    public function testEmptyPort(){
        $context = new ServerContext( '1.1.1.1', NULL );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 4
     */
    public function testStringPort(){
        $context = new ServerContext( '1.1.1.1', '10foo' );
    }

    /**
     * @expectedException \OutOfRangeException
     * @expectedExceptionCode 4
     */
    public function testToBigPort(){
        $context = new ServerContext( '1.1.1.1', PHP_INT_MAX );
    }


}
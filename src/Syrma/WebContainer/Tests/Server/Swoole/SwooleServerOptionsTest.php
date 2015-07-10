<?php

namespace Syrma\WebContainer\Tests\Server\Swoole;

use Syrma\WebContainer\Server\Swoole\SwooleServerOptions;

class SwooleServerOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testPositive()
    {
        $opt = new SwooleServerOptions(array(
            'chroot' => 'foo',
            'daemonize' => true,
            'backlog' => 10,
            'cpu_affinity_ignore' => array(1 => 99),
        ));

        $opt->setOption('user', 'bar');

        $this->assertSame(array(
            'chroot' => 'foo',
            'daemonize' => true,
            'backlog' => 10,
            'cpu_affinity_ignore' => array(1 => 99),
            'user' => 'bar',
        ), $opt->getOptions());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 1
     */
    public function testNameNotExists()
    {
        new SwooleServerOptions(array(
            'chroot-abd' => 'foo',
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 1
     */
    public function testNameNotExistsWithSetter()
    {
        $opt = new SwooleServerOptions();
        $opt->setOption('foo', 'bar');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 2
     */
    public function testInvalidInt()
    {
        new SwooleServerOptions(array(
            'backlog' => 'foo',
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 2
     */
    public function testInvalidBool()
    {
        new SwooleServerOptions(array(
            'backlog' => 'foo',
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 2
     */
    public function testInvalidString()
    {
        new SwooleServerOptions(array(
            'chroot' => array('foo' => 'bar'),
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 2
     */
    public function testInvalidArray()
    {
        new SwooleServerOptions(array(
            'cpu_affinity_ignore' => new \stdClass(),
        ));
    }
}

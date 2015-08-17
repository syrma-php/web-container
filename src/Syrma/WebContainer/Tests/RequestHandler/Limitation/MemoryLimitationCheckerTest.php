<?php

namespace Syrma\WebContainer\Tests\RequestHandler\Limitation;

use Syrma\WebContainer\Exception\ServerStopExceptionInterface;
use Syrma\WebContainer\RequestHandler\Limitation\MemoryLimitationChecker;

class MemoryLimitationCheckerTest extends \PHPUnit_Framework_TestCase
{
    private $memoryBlock = '';

    protected function tearDown()
    {
        parent::tearDown();
        $this->memoryBlock = '';
    }

    protected function allocMemory($byte)
    {
        $this->memoryBlock = str_repeat('x', $byte);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadSuffix()
    {
        new MemoryLimitationChecker('3C');
    }

    /**
     * @expectedException \Syrma\WebContainer\Exception\ServerStopExceptionInterface
     */
    public function testMinimalMemory()
    {
        (new MemoryLimitationChecker(100))->checkLimit();
    }

    public function testSettedValue()
    {
        $limit = (ceil(memory_get_usage(true) / (1024 * 1024)) + 1).'M';
        $checker = new MemoryLimitationChecker($limit);
        $checker->checkLimit(); // no exception

        $this->allocMemory(2 * 1024 * 1024);

        try {
            $checker->checkLimit();
            $this->fail('The checker not throw exception!');
        } catch (ServerStopExceptionInterface $ex) {
        }
    }

    public function testCheckIniValue()
    {
        $oldLimit = ini_get('memory_limit');
        try {
            $limit = (ceil(memory_get_usage(true) / (1024 * 1024)) + 1).'M';
            ini_set('memory_limit', $limit);

            $checker = new MemoryLimitationChecker();
        } finally {
            ini_set('memory_limit', $oldLimit);
        }

        $checker->checkLimit(); // no exception
        $this->allocMemory(2 * 1024 * 1024);

        try {
            $checker->checkLimit();
            $this->fail('The checker not throw exception!');
        } catch (ServerStopExceptionInterface $ex) {
        }
    }

    public function testUnlimited()
    {
        $oldLimit = ini_get('memory_limit');
        try {
            ini_set('memory_limit', '-1');
            $checker = new MemoryLimitationChecker();
        } finally {
            ini_set('memory_limit', $oldLimit);
        }

        $checker->checkLimit(); // no exception
        $this->allocMemory(5 * 1024 * 1024);
        $checker->checkLimit();
    }
}

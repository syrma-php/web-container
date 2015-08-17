<?php

namespace Syrma\WebContainer\Tests\RequestHandler\Limitation;

use Syrma\WebContainer\RequestHandler\Limitation\ExecutionTimeLimitationChecker;

class ExecutionTimeLimitationCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Syrma\WebContainer\Exception\ServerStopExceptionInterface
     */
    public function testWithRelativeInt()
    {
        (new ExecutionTimeLimitationChecker(-1))->checkLimit();
    }

    /**
     * @expectedException \Syrma\WebContainer\Exception\ServerStopExceptionInterface
     */
    public function testWithString()
    {
        (new ExecutionTimeLimitationChecker('-1minute'))->checkLimit();
    }

    public function testWithAbsuluteInt()
    {
        (new ExecutionTimeLimitationChecker(time() + 5))->checkLimit();
    }
}

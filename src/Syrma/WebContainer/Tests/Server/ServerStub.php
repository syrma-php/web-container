<?php

namespace Syrma\WebContainer\Tests\Server;

use Syrma\WebContainer\RequestHandlerInterface;
use Syrma\WebContainer\ServerContextInterface;
use Syrma\WebContainer\ServerInterface;

/**
 * Server for tests.
 */
class ServerStub implements ServerInterface
{
    /**
     * @var callable
     */
    private $startFn;

    /**
     * @param callable $startFn
     */
    public function __construct(callable $startFn)
    {
        $this->startFn = $startFn;
    }

    /**
     * {@inheritdoc}
     */
    public function start(ServerContextInterface $context, RequestHandlerInterface $requestHandler)
    {
        return call_user_func($this->startFn, $context, $requestHandler);
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        //nothing
    }

    /**
     * {@inheritdoc}
     */
    public static function isAvaiable()
    {
        return true;
    }
}

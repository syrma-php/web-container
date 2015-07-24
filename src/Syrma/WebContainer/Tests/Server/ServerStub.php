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
     * @var callable|null
     */
    private $stopFn;

    /**
     * @param callable      $startFn
     * @param callable|null $stopFn
     */
    public function __construct(callable $startFn, callable $stopFn = null)
    {
        $this->startFn = $startFn;
        $this->stopFn = $stopFn;
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
        if (null !== $this->stopFn) {
            call_user_func($this->stopFn);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function isAvaiable()
    {
        return true;
    }
}

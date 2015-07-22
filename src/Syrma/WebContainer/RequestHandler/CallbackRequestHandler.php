<?php

namespace Syrma\WebContainer\RequestHandler;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Syrma\WebContainer\RequestHandlerInterface;

/**
 * Callback request handler.
 */
class CallbackRequestHandler implements RequestHandlerInterface
{
    /**
     * @var callable
     */
    private $handler;
    /**
     * @var callable|null
     */
    private $finish;

    /**
     * @param callable      $handler
     * @param callable|null $finish
     */
    public function __construct(callable $handler, callable $finish = null)
    {
        $this->handler = $handler;
        $this->finish = $finish;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request)
    {
        return call_user_func($this->handler, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function finish(RequestInterface $request, ResponseInterface $response)
    {
        if (null !== $this->finish) {
            call_user_func($this->finish, $request, $response);
        }
    }
}

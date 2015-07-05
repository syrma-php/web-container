<?php


namespace Syrma\WebContainer\RequestHandler;

use Psr\Http\Message\RequestInterface;

use Syrma\WebContainer\RequestHandlerInterface;

/**
 * Callback request handler
 */
class CallbackRequestHandler implements RequestHandlerInterface{

    /**
     * @var callable
     */
    private $handler;

    /**
     * @param callable $handler
     */
    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }


    /**
     * {@inheritdoc}
     */
    public function handle( RequestInterface $request )
    {
        return call_user_func($this->handler, $request);
    }
}
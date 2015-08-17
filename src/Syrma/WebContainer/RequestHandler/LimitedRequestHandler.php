<?php

namespace Syrma\WebContainer\RequestHandler;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Syrma\WebContainer\RequestHandler\Limitation\LimitationCheckerInterface;
use Syrma\WebContainer\RequestHandlerInterface;

/**
 * The limited request handler. It is run all limit checker at finish request.
 */
class LimitedRequestHandler implements RequestHandlerInterface
{
    /**
     * @var RequestHandlerInterface
     */
    private $innerHandler;

    /**
     * @var LimitationCheckerInterface[]
     */
    private $checkers = array();

    /**
     * @param RequestHandlerInterface      $innerHandler
     * @param LimitationCheckerInterface[] $checkers
     */
    public function __construct(RequestHandlerInterface $innerHandler, array $checkers = array())
    {
        $this->innerHandler = $innerHandler;
        foreach ($checkers as $checker) {
            $this->addChecker($checker);
        }
    }

    /**
     * @param LimitationCheckerInterface $checker
     */
    public function addChecker(LimitationCheckerInterface $checker)
    {
        $this->checkers[] = $checker;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request)
    {
        return $this->innerHandler->handle($request);
    }

    /**
     * {@inheritdoc}
     */
    public function finish(RequestInterface $request, ResponseInterface $response)
    {
        $this->innerHandler->finish($request, $response);

        foreach ($this->checkers as $checker) {
            $checker->checkLimit();
        }
    }
}

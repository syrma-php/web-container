<?php

namespace Syrma\WebContainer\RequestHandler;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Syrma\WebContainer\RequestHandlerInterface;

/**
 * RequestHandler for Symfony2.
 */
class SymfonyRequestHandler implements RequestHandlerInterface
{
    /**
     * @var HttpKernelInterface
     */
    private $kernel;

    /**
     * @var bool
     */
    private $isTerminableKernel;

    /**
     * @var HttpFoundationFactoryInterface
     */
    private $httpFoundationFactory;

    /**
     * @var HttpMessageFactoryInterface
     */
    private $httpMessageFactory;

    /**
     * @param HttpKernelInterface            $kernel
     * @param HttpFoundationFactoryInterface $httpFoundationFactory
     * @param HttpMessageFactoryInterface    $httpMessageFactory
     */
    public function __construct(
        HttpKernelInterface $kernel,
        HttpFoundationFactoryInterface $httpFoundationFactory,
        HttpMessageFactoryInterface $httpMessageFactory
    ) {
        $this->kernel = $kernel;
        $this->isTerminableKernel = $this->kernel instanceof TerminableInterface;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->httpMessageFactory = $httpMessageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request)
    {   /* @var  ServerRequestInterface $request */
        $sfRequest = $this->httpFoundationFactory->createRequest($request);
        $sfResponse = $this->kernel->handle($sfRequest);

        if ($this->isTerminableKernel) {
            $this->kernel->terminate($sfRequest, $sfResponse);
        }

        return $this->httpMessageFactory->createResponse($sfResponse);
    }
}

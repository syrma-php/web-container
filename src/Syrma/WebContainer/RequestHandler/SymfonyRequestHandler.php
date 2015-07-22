<?php

namespace Syrma\WebContainer\RequestHandler;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
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
     * @var \SplObjectStorage
     */
    private $requestMapping;

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
        $this->requestMapping = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request)
    {   /* @var  ServerRequestInterface $request */
        $sfRequest = $this->httpFoundationFactory->createRequest($request);
        $sfResponse = $this->kernel->handle($sfRequest);

        if ($this->isTerminableKernel) {
            $this->requestMapping->attach($request, array($sfRequest, $sfResponse));
        }

        return $this->httpMessageFactory->createResponse($sfResponse);
    }

    /**
     * {@inheritdoc}
     */
    public function finish(RequestInterface $request, ResponseInterface $response)
    {
        if ($this->isTerminableKernel) {
            list($sfRequest, $sfResponse) = $this->requestMapping->offsetGet($request);
            $this->requestMapping->detach($request);

            $kernel = $this->kernel;
            /* @var $kernel TerminableInterface */
            $kernel->terminate($sfRequest, $sfResponse);
        }
    }

    /**
     * Helper method for testing.
     *
     * @internal
     *
     * @return int
     */
    public function getRequestMappingCount()
    {
        return count($this->requestMapping);
    }
}

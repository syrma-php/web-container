<?php


namespace Syrma\WebContainer;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Handle the server requests
 */
interface RequestHandlerInterface {

    /**
     * Handle the server request
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle( RequestInterface $request );
}
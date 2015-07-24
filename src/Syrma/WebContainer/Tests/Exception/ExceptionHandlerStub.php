<?php

namespace Syrma\WebContainer\Tests\Exception;

use Syrma\WebContainer\ExceptionHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class ExceptionHandlerStub implements ExceptionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception)
    {
        if (null !== $preEx = $exception->getPrevious()) {
            throw $preEx;
        }

        throw $exception;
    }

    /**
     * {@inheritdoc}
     */
    public function createErrorResponseByException(\Exception $exception = null)
    {
        $stream = new Stream('php://temp', 'r+');
        $stream->write($exception->getMessage());

        return new Response($stream);
    }
}

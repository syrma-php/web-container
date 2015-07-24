<?php

namespace Syrma\WebContainer\Exception;

use Psr\Http\Message\ResponseInterface;

/**
 * Special exception for server stopping.
 */
class ServerStopWithResponseException extends \RuntimeException implements ServerStopExceptionInterface, ResponseAwareExceptionInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(ResponseInterface $response, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return $this->response;
    }
}

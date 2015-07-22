<?php

namespace Syrma\WebContainer\Exception;

use Psr\Http\Message\ResponseInterface;

/**
 * Implementation for ServerStopExceptionInterface.
 */
class ServerStopException extends \RuntimeException implements ServerStopExceptionInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param ResponseInterface $response
     * @param string            $message
     * @param int               $code
     * @param \Exception        $previous
     */
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

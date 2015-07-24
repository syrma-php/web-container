<?php

namespace Syrma\WebContainer\Exception;

/**
 * Default implementation of HttpException.
 */
class HttpException extends \RuntimeException implements HttpExceptionInterface
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array
     */
    private $headers;

    /**
     * @param string     $statusCode
     * @param array      $headers
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($statusCode, array $headers = array(), $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}

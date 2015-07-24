<?php

namespace Syrma\WebContainer\Util;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Psr7 component factory.
 */
interface Psr7FactoryInterface
{
    /**
     * @param null|string                     $uri     URI for the request, if any.
     * @param null|string                     $method  HTTP method for the request, if any.
     * @param string|resource|StreamInterface $body    Message body, if any.
     * @param array                           $headers Headers for the message, if any.
     *
     * @throws \InvalidArgumentException for any invalid value.
     *
     * @return RequestInterface
     */
    public function createRequest($uri = null, $method = null, $body = 'php://temp', array $headers = []);

    /**
     * @param array                           $serverParams  Server parameters, typically from $_SERVER
     * @param array                           $uploadedFiles Upload file information, a tree of UploadedFiles
     * @param null|string                     $uri           URI for the request, if any.
     * @param null|string                     $method        HTTP method for the request, if any.
     * @param string|resource|StreamInterface $body          Message body, if any.
     * @param array                           $headers       Headers for the message, if any.
     *
     * @throws \InvalidArgumentException for any invalid value.
     *
     * @return ServerRequestInterface
     */
    public function createServerRequest(
        array $serverParams = [],
        array $uploadedFiles = [],
        $uri = null,
        $method = null,
        $body = 'php://temp',
        array $headers = []
    );

    /**
     * @param string|resource $streamOrFile
     * @param int             $size
     * @param int             $errorStatus
     * @param null|string     $clientFilename
     * @param null|string     $clientMediaType
     *
     * @return UploadedFileInterface
     */
    public function createUploadedFile($streamOrFile, $size, $errorStatus, $clientFilename = null, $clientMediaType = null);

    /**
     * @param string|resource|StreamInterface $stream  Stream identifier and/or actual stream resource
     * @param int                             $status  Status code for the response, if any.
     * @param array                           $headers Headers for the response, if any.
     *
     * @throws \InvalidArgumentException on any invalid element.
     *
     * @return ResponseInterface
     */
    public function createResponse($stream = 'php://memory', $status = 200, array $headers = []);
}

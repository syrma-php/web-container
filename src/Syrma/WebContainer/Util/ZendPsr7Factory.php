<?php

namespace Syrma\WebContainer\Util;

use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;
use Zend\Diactoros\UploadedFile;

/**
 * ZendDiactoros implementation.
 */
class ZendPsr7Factory implements Psr7FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRequest($uri = null, $method = null, $body = 'php://temp', array $headers = [])
    {
        return new Request($uri, $method, $body, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function createServerRequest(
        array $serverParams = [],
        array $uploadedFiles = [],
        $uri = null,
        $method = null,
        $body = 'php://temp',
        array $headers = []
    ) {
        return new ServerRequest($serverParams, $uploadedFiles, $uri, $method, $body, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function createUploadedFile($streamOrFile, $size, $errorStatus, $clientFilename = null, $clientMediaType = null)
    {
        return new UploadedFile($streamOrFile, $size, $errorStatus, $clientFilename, $clientMediaType);
    }

    /**
     * {@inheritdoc}
     */
    public function createResponse($stream = 'php://memory', $status = 200, array $headers = [])
    {
        return new Response($stream, $status, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function createStream($mode = 'r+')
    {
        return new Stream('php://memory', $mode);
    }
}

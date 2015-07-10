<?php

namespace Syrma\WebContainer\Util;

use Zend\Diactoros\Request;
use Zend\Diactoros\ServerRequest;
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
}

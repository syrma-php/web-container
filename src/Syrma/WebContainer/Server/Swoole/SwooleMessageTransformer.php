<?php

namespace Syrma\WebContainer\Server\Swoole;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Syrma\WebContainer\Util\Psr7FactoryInterface;

/**
 * Transform Psr7 messages to Swoole data.
 *
 * transform: SwooleRequest -> Psr7Request
 * reverseTransform: Psr7Response -> SwooleResponse
 */
class SwooleMessageTransformer
{
    /**
     * @var Psr7FactoryInterface
     */
    private $psr7Factory;

    /**
     * @var bool
     */
    private $useServerRequest;

    /**
     * @var int
     */
    private $responseBuffer;

    /**
     * @param Psr7FactoryInterface $psr7FactoryInterface
     * @param bool                 $useServerRequest
     * @param int                  $responseBuffer
     */
    public function __construct(Psr7FactoryInterface $psr7FactoryInterface, $useServerRequest = true, $responseBuffer = 8096)
    {
        $this->psr7Factory = $psr7FactoryInterface;
        $this->useServerRequest = (bool) $useServerRequest;
        $this->responseBuffer = (int) $responseBuffer;
    }

    /**
     * @param \swoole_http_request $swooleRequest
     *
     * @return RequestInterface
     */
    public function transform(\swoole_http_request $swooleRequest)
    {
        return $this->useServerRequest ?
            $this->transformServerRequest($swooleRequest) : $this->transformRequest($swooleRequest);
    }

    /**
     * @param \swoole_http_request $swooleRequest
     *
     * @return RequestInterface
     */
    private function transformRequest(\swoole_http_request $swooleRequest)
    {
        return $this->psr7Factory->createRequest(
            $this->transformUri($swooleRequest),
            $this->transformRequestMethod($swooleRequest),
            $this->transformBody($swooleRequest),
            $this->transformHeader($swooleRequest)
        )
            ->withProtocolVersion($this->transformProtocolVersion($swooleRequest))
        ;
    }

    /**
     * @param \swoole_http_request $swooleRequest
     *
     * @return ServerRequestInterface
     */
    private function transformServerRequest(\swoole_http_request $swooleRequest)
    {
        return $this->psr7Factory->createServerRequest(
            $this->transformServerParams($swooleRequest),
            $this->transformUpladedFiles($swooleRequest),
            $this->transformUri($swooleRequest),
            $this->transformRequestMethod($swooleRequest),
            $this->transformBody($swooleRequest),
            $this->transformHeader($swooleRequest)
        )
            ->withCookieParams($this->transformCookies($swooleRequest))
            ->withProtocolVersion($this->transformProtocolVersion($swooleRequest))
        ;
    }

    /**
     * @param ResponseInterface     $response
     * @param \swoole_http_response $swooleResponse
     */
    public function reverseTransform(ResponseInterface $response, \swoole_http_response $swooleResponse)
    {
        foreach (array_keys($response->getHeaders()) as $name) {
            $swooleResponse->header($name, $response->getHeaderLine($name));
        }

        $swooleResponse->status($response->getStatusCode());

        $body = $response->getBody();
        $body->rewind();

        # workaround for https://bugs.php.net/bug.php?id=68948
        while (false === $body->eof() && '' !== $buffer = $body->read($this->responseBuffer)) {
            $swooleResponse->write($buffer);
        }

        $swooleResponse->end();
    }

    /**
     * @param \swoole_http_request $swooleRequest
     *
     * @return array
     */
    private function transformServerParams(\swoole_http_request $swooleRequest)
    {
        $serverParams = array_change_key_case($swooleRequest->server, \CASE_UPPER);

        foreach ($swooleRequest->header as $name => $value) {
            $serverParams['HTTP_'.strtoupper($name)] = $value;
        }

        return $serverParams;
    }

    /**
     * @param \swoole_http_request $swooleRequest
     *
     * @return string
     */
    private function transformUri(\swoole_http_request $swooleRequest)
    {
        //Scheme
        if (isset($swooleRequest->header['x-forwarded-proto'])) {
            $scheme = $swooleRequest->header['x-forwarded-proto'];
        } elseif (isset($swooleRequest->server['https']) && 'off' !== $swooleRequest->server['https']) {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }

        //Host
        if (isset($swooleRequest->header['x-forwarded-host'])) {
            $hostAndPort = $swooleRequest->header['x-forwarded-host'];
        } elseif (isset($swooleRequest->header['host'])) {
            $hostAndPort = $swooleRequest->header['host'];
        } else {
            $hostAndPort = 'localhost';
        }

        //requestUri
        if (isset($swooleRequest->server['request_uri'])) {
            $path = $swooleRequest->server['request_uri'];
        } else {
            $path = '/';
        }

        $uri = $scheme.'://'.$hostAndPort.$path;
        if (isset($swooleRequest->server['query_string'])) {
            $uri .= '?'.$swooleRequest->server['query_string'];
        }

        return $uri;
    }

    /**
     * @param \swoole_http_request $swooleRequest
     *
     * @return StreamInterface
     */
    private function transformBody(\swoole_http_request $swooleRequest)
    {
        $body = $this->psr7Factory->createStream();

        if (isset($swooleRequest->fd) && false !== $rawContent = $swooleRequest->rawContent()) {
            $body->write($rawContent);
            $body->rewind();
            unset($rawContent);
        }

        return $body;
    }

    /**
     * @param \swoole_http_request $swooleRequest
     *
     * @return string
     */
    private function transformProtocolVersion(\swoole_http_request $swooleRequest)
    {
        if (
            isset($swooleRequest->server['server_protocol']) &&
            0 !== preg_match('+/(?P<ver>\d\.\d)$+', $swooleRequest->server['server_protocol'], $matches)
        ) {
            return $matches['ver'];
        } else {
            return '1.1';
        }
    }

    /**
     * @param \swoole_http_request $swooleRequest
     *
     * @return array
     */
    private function transformUpladedFiles(\swoole_http_request $swooleRequest)
    {
        if (!isset($swooleRequest->files)) {
            return array();
        }

        $files = array();

        foreach ($swooleRequest->files as $name => $file) {
            $files[$name] = $this->psr7Factory->createUploadedFile(
                $file['tmp_name'],
                $file['size'],
                $file['error'],
                $file['name'],
                $file['type']
            );
        }

        return $files;
    }

    /**
     * @param \swoole_http_request $swooleRequest
     *
     * @return array
     */
    private function transformCookies(\swoole_http_request $swooleRequest)
    {
        return isset($swooleRequest->cookie) ? (array) $swooleRequest->cookie : array();
    }

    /**
     * @param \swoole_http_request $swooleRequest
     *
     * @return string
     */
    private function transformRequestMethod(\swoole_http_request $swooleRequest)
    {
        return isset($swooleRequest->server['request_method']) ?
            $swooleRequest->server['request_method'] : 'GET';
    }

    /**
     * @param \swoole_http_request $swooleRequest
     *
     * @return array
     */
    private function transformHeader(\swoole_http_request $swooleRequest)
    {
        $headers = array();
        foreach ($swooleRequest->header as $name => $value) {
            $headers[ strtr(ucwords(strtr($name, array('-' => ' '))), array(' ' => '-')) ] = $value;
        }

        return $headers;
    }
}

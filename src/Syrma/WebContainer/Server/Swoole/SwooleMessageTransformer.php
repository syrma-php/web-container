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
            $swooleRequest->server['request_method'],
            $this->transformBody($swooleRequest),
            array_change_key_case($swooleRequest->header, CASE_UPPER)
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
            $swooleRequest->server,
            $this->transformUpladedFiles($swooleRequest),
            $this->transformUri($swooleRequest),
            $swooleRequest->server['request_method'],
            $this->transformBody($swooleRequest),
            array_change_key_case($swooleRequest->header, CASE_UPPER)
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
        foreach ($response->getHeaders() as $key => $values) {
            foreach ($values as $value) {
                $swooleResponse->header($key, $value);
            }
        }

        $swooleResponse->status($response->getStatusCode());

        $body = $response->getBody();
        while (false === $body->eof()) {
            $swooleResponse->write($body->read($this->responseBuffer));
        }
        $swooleResponse->end();
    }

    /**
     * @param \swoole_http_request $swooleRequest
     *
     * @return string
     */
    private function transformUri(\swoole_http_request $swooleRequest)
    {
        $uri = $swooleRequest->header['host'].$swooleRequest->server['request_uri'];
        if (isset($swooleRequest->server['query_string'])) {
            $uri .= '?'.$swooleRequest->server['query_string'];
        }

        return $uri;
    }

    /**
     * @param \swoole_http_request $swooleRequest
     *
     * @return resource|string
     */
    private function transformBody(\swoole_http_request $swooleRequest)
    {
        if (false !== $rawContent = $swooleRequest->rawContent()) {
            $body = fopen('php://temp', 'wb+');
            fwrite($body, $rawContent);
            fseek($body, 0);
            unset($rawContent);
        } else {
            $body = 'php://temp';
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
        if (false !== preg_match('+/(?P<ver>\d\.\d)$+', $swooleRequest->server['server_protocol'], $matches)) {
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
}

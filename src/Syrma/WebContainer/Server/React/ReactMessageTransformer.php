<?php

namespace Syrma\WebContainer\Server\React;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use React\Http\Request as ReactRequest;
use React\Http\Response as ReactResponse;
use Syrma\WebContainer\Util\Psr7FactoryInterface;

class ReactMessageTransformer
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
     * @var string
     */
    private $argSeparator;

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
        $this->argSeparator = ini_get('arg_separator.output');
    }

    /**
     * @param ReactRequest $reactRequest
     *
     * @return RequestInterface
     */
    public function transform(ReactRequest $reactRequest)
    {
        return $this->useServerRequest ?
            $this->transformServerRequest($reactRequest) : $this->transformRequest($reactRequest);
    }

    /**
     * @param ReactRequest $reactRequest
     *
     * @return RequestInterface
     */
    private function transformRequest(ReactRequest $reactRequest)
    {
        return $this->psr7Factory->createRequest(
            $this->transformUri($reactRequest),
            $reactRequest->getMethod(),
            $this->transformBody($reactRequest),
            $reactRequest->getHeaders()
        )
            ->withProtocolVersion($reactRequest->getHttpVersion())
            ;
    }

    /**
     * @param ReactRequest $reactRequest
     *
     * @return ServerRequestInterface
     */
    private function transformServerRequest(ReactRequest $reactRequest)
    {
        return $this->psr7Factory->createServerRequest(
            $this->transformServerParams($reactRequest),
            $this->transformUpladedFiles($reactRequest),
            $this->transformUri($reactRequest),
            $reactRequest->getMethod(),
            $this->transformBody($reactRequest),
            $reactRequest->getHeaders()
        )
            ->withParsedBody($reactRequest->getPost())
            ->withCookieParams($this->transformCookies($reactRequest))
            ->withProtocolVersion($reactRequest->getHttpVersion())
        ;
    }

    /**
     * @param ResponseInterface $response
     * @param ReactResponse     $reactResponse
     */
    public function reverseTransform(ResponseInterface $response, ReactResponse $reactResponse)
    {
        $body = $response->getBody();
        $body->rewind();

        $headers = array();
        foreach (array_keys($response->getHeaders()) as $name) {
            $headers[$name] = $response->getHeaderLine($name);
        }

        if (!isset($headers['Content-Length']) && $body->getSize() > 0) {
            $headers['Content-Length'] = $body->getSize();
        }

        $reactResponse->writeHead($response->getStatusCode(), $headers);

        while (false === $body->eof()) {
            $reactResponse->write($body->read($this->responseBuffer));
        }
        $reactResponse->end();
    }

    /**
     * @param ReactRequest $reactRequest
     *
     * @return array
     */
    private function transformServerParams(ReactRequest $reactRequest)
    {
        $serverParams = array(
            'REQUEST_METHOD' => $reactRequest->getMethod(),
            'REQUEST_URI' => $this->transformRequestUri($reactRequest),
            'SERVER_PROTOCOL' => 'HTTP/'.$reactRequest->getHttpVersion(),
            'SERVER_SOFTWARE' => 'reactphp-http',
            'REMOTE_ADDR' => $reactRequest->remoteAddress,
        );

        foreach ($reactRequest->getHeaders() as $name => $value) {
            $serverParams['HTTP_'.strtoupper($name)] = $value;
        }

        return $serverParams;
    }

    /**
     * @param ReactRequest $reactRequest
     *
     * @return string
     */
    private function transformUri(ReactRequest $reactRequest)
    {
        $headers = $reactRequest->getHeaders();

        //Scheme
        if (isset($headers['X-Forwarded-Proto'])) {
            $scheme = $headers['X-Forwarded-Proto'];
        } elseif (isset($headers['Https']) && 'off' !== $headers['Https']) {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }

        //Host
        if (isset($headers['X-Forwarded-Host'])) {
            $hostAndPort = $headers['X-Forwarded-Host'];
        } elseif (isset($headers['Host'])) {
            $hostAndPort = $headers['Host'];
        } else {
            $hostAndPort = 'localhost';
        }

        return $scheme.'://'.$hostAndPort.$this->transformRequestUri($reactRequest);
    }

    /**
     * @param ReactRequest $reactRequest
     *
     * @return string
     */
    private function transformRequestUri(ReactRequest $reactRequest)
    {
        $uri = $reactRequest->getPath();
        if (array() !== $query = (array) $reactRequest->getQuery()) {
            $uri .= '?'.http_build_query($query, '', $this->argSeparator, PHP_QUERY_RFC3986);
        }

        return $uri;
    }

    /**
     * @param ReactRequest $reactRequest
     *
     * @return StreamInterface
     */
    private function transformBody(ReactRequest $reactRequest)
    {
        $body = $this->psr7Factory->createStream();
        $body->write((string) $reactRequest->getBody());
        $body->rewind();

        return $body;
    }

    /**
     * @param ReactRequest $reactRequest
     *
     * @return array
     */
    private function transformUpladedFiles(ReactRequest $reactRequest)
    {
        $factory = function (array $fileInfo) use (&$factory) {

            $files = array();
            foreach ($fileInfo as $name => &$attr) {
                if (is_array($attr) && isset($attr['stream'])) {
                    $files[$name] = $this->psr7Factory->createUploadedFile(
                        $attr['stream'],
                        $attr['size'],
                        $attr['error'],
                        $attr['name'],
                        $attr['type']
                    );
                } elseif (is_array($attr)) {
                    $files[$name] = $factory($attr);
                } else {
                    throw new \InvalidArgumentException('Invalid value in files specification');
                }
            }

            return $files;

        };

        return $factory($reactRequest->getFiles());
    }

    /**
     * @param ReactRequest $reactRequest
     *
     * @return array
     */
    private function transformCookies(ReactRequest $reactRequest)
    {
        $cookies = array();
        $headers = $reactRequest->getHeaders();

        if (isset($headers['Cookie'])) {
            foreach (explode(';', $headers['Cookie']) as $rawCookie) {
                $segment = explode('=', $rawCookie, 2);
                $cookies[trim($segment[0])] = isset($segment[1]) ? $segment[1] : null;
            }
        }

        return $cookies;
    }
}

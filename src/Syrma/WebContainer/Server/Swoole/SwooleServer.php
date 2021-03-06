<?php

namespace Syrma\WebContainer\Server\Swoole;

use Syrma\WebContainer\RequestHandlerInterface;
use Syrma\WebContainer\ServerContextInterface;
use Syrma\WebContainer\ServerInterface;

/**
 * Swoole Server.
 */
class SwooleServer implements ServerInterface
{
    /**
     * @var \swoole_http_server
     */
    private $server;

    /**
     * @var SwooleMessageTransformer
     */
    private $messageTransformer;

    /**
     * @var SwooleServerOptions
     */
    private $serverOptions;

    /**
     * @var int
     */
    private $parentPid;

    /**
     * @param SwooleMessageTransformer $messageTransformer
     * @param SwooleServerOptions      $serverOptions
     */
    public function __construct(SwooleMessageTransformer $messageTransformer, SwooleServerOptions $serverOptions = null)
    {
        $this->messageTransformer = $messageTransformer;
        $this->serverOptions = $serverOptions;
        $this->parentPid = posix_getpid();
    }

    /**
     * {@inheritdoc}
     */
    public function start(ServerContextInterface $context, RequestHandlerInterface $requestHandler)
    {
        $this->server = new \swoole_http_server($context->getListenAddress(), $context->getListenPort(), \SWOOLE_BASE);

        if (null !== $this->serverOptions) {
            $this->server->set($this->serverOptions->getOptions());
        }

        $this->server->on('request', $this->createRequestHandler($requestHandler));
        $this->server->start();
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $pid = posix_getpid();

        if ($this->parentPid != $pid) {
            //kill the thread
            posix_kill($pid, SIGTERM);  //TODO
        } elseif ($this->parentPid == $pid && null !== $this->server) {
            $this->server->shutdown();
            $this->server = null;
        }
    }

    /**
     * @param RequestHandlerInterface $requestHandler
     *
     * @return callable
     */
    private function createRequestHandler(RequestHandlerInterface $requestHandler)
    {
        return function (\swoole_http_request $swooleRequest, \swoole_http_response $swooleResponse) use ($requestHandler) {
            $request = $this->messageTransformer->transform($swooleRequest);
            $response = $requestHandler->handle($request);
            $this->messageTransformer->reverseTransform($response, $swooleResponse);

            $requestHandler->finish($request, $response);
        };
    }

    /**
     * {@inheritdoc}
     */
    public static function isAvaiable()
    {
        return class_exists('\swoole_http_server');
    }
}

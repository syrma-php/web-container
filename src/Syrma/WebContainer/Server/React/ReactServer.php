<?php

namespace Syrma\WebContainer\Server\React;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use React\Socket\ServerInterface as SocketServerInterface;
use React\Http\ServerInterface as HttpServerInterface;
use React\Http\Request as ReactRequest;
use React\Http\Response as ReactResponse;
use Syrma\WebContainer\RequestHandlerInterface;
use Syrma\WebContainer\ServerContextInterface;
use Syrma\WebContainer\ServerInterface;

/**
 * React Server.
 */
class ReactServer implements ServerInterface
{
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var SocketServerInterface
     */
    private $socketServer;

    /**
     * @var HttpServerInterface
     */
    private $httpServer;

    /**
     * @var ReactMessageTransformer
     */
    private $messageTransformer;

    /**
     * @param ReactMessageTransformer $messageTransformer
     */
    public function __construct(ReactMessageTransformer $messageTransformer)
    {
        $this->messageTransformer = $messageTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function start(ServerContextInterface $context, RequestHandlerInterface $requestHandler)
    {
        $this->loop = Factory::create();
        $this->socketServer = new SocketServer($this->loop);
        $this->httpServer = new HttpServer($this->socketServer);

        $this->httpServer->on('request', $this->createRequestHandler($requestHandler));
        $this->socketServer->listen($context->getListenPort(), $context->getListenAddress());

        $this->loop->run();
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        if (null !== $this->loop) {
            $this->loop->stop();

            $this->httpServer->removeAllListeners();
            $this->httpServer = null;

            $this->socketServer->shutdown();
            $this->socketServer = null;

            $this->loop = null;
        }
    }

    /**
     * @param RequestHandlerInterface $requestHandler
     *
     * @return callable
     */
    private function createRequestHandler(RequestHandlerInterface $requestHandler)
    {
        return function (ReactRequest $reactRequest,  ReactResponse $reactResponse) use ($requestHandler) {
            $request = $this->messageTransformer->transform($reactRequest);
            $response = $requestHandler->handle($request);
            $this->messageTransformer->reverseTransform($response, $reactResponse);

            $requestHandler->finish($request, $response);

        };
    }

    /**
     * {@inheritdoc}
     */
    public static function isAvaiable()
    {
        return class_exists('\React\Http\Server');
    }
}

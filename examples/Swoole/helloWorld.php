<?php


require dirname(dirname(__DIR__)).'/vendor/autoload.php';

    $transformer = new \Syrma\WebContainer\Server\Swoole\SwooleMessageTransformer(
        new \Syrma\WebContainer\Util\ZendPsr7Factory()
    );

    $options = new \Syrma\WebContainer\Server\Swoole\SwooleServerOptions(array(
        'daemonize' => false,
        'worker_num' => 1,
    ));

    $executor = new \Syrma\WebContainer\Executor(
        new \Syrma\WebContainer\Server\Swoole\SwooleServer($transformer, $options),
        new \Syrma\WebContainer\RequestHandler\CallbackRequestHandler(function (\Psr\Http\Message\RequestInterface $request) {

            $stream = new \Zend\Diactoros\Stream('php://temp', 'wb+');
            $stream->write('Hello World!');
            $stream->rewind();

            return new \Zend\Diactoros\Response($stream, 200, ['X-Debug-token' => md5(1)]);
        })
    );

    $executor->execute(new \Syrma\WebContainer\ServerContext());

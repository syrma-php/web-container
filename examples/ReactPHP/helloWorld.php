<?php


use Monolog\Handler\StreamHandler;
use Syrma\WebContainer\Server\React\ReactMessageTransformer;

require dirname(dirname(__DIR__)).'/vendor/autoload.php';

    $logger = new \Monolog\Logger('app',array( new StreamHandler(fopen('php://stdout', 'w'))));

    $transformer = new ReactMessageTransformer(
        new \Syrma\WebContainer\Util\ZendPsr7Factory()
    );

    $executor = new \Syrma\WebContainer\Executor(
        new \Syrma\WebContainer\Server\React\ReactServer($transformer),
        new \Syrma\WebContainer\RequestHandler\CallbackRequestHandler(function (\Psr\Http\Message\RequestInterface $request) {

            $stream = new \Zend\Diactoros\Stream('php://temp', 'wb+');
            $stream->write('Hello World! - ' . posix_getpid() . PHP_EOL);
            $response = new \Zend\Diactoros\Response($stream, 200, ['X-Debug-token' => md5(1)]);

            return $response;

        }),
        $logger
    );

    $executor->execute(new \Syrma\WebContainer\ServerContext());

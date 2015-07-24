<?php

require dirname(dirname(__DIR__)).'/vendor/autoload.php';

    $transformer = new \Syrma\WebContainer\Server\Swoole\SwooleMessageTransformer(
        new \Syrma\WebContainer\Util\ZendPsr7Factory()
    );

    $options = new \Syrma\WebContainer\Server\Swoole\SwooleServerOptions(array(
        'daemonize' => false,
        'worker_num' => 1,
    ));

    $exceptionHandler = new \Syrma\WebContainer\Exception\DefaultExceptionHandler(
        new \Syrma\WebContainer\Util\ZendPsr7Factory(),
        \Syrma\WebContainer\Util\ErrorPageLoader::createDefault()
    );

    $requestHandler = function (\Psr\Http\Message\RequestInterface $request) {

        $stream = new \Zend\Diactoros\Stream('php://temp', 'r+');
        $stream->write('Hello World! - ' . posix_getpid() . PHP_EOL);
        $response = new \Zend\Diactoros\Response($stream, 200, ['X-Debug-token' => md5(1)]);

        return $response;
    };

    $executor = new \Syrma\WebContainer\Executor(
        new \Syrma\WebContainer\Server\Swoole\SwooleServer($transformer, $options),
        new \Syrma\WebContainer\RequestHandler\CallbackRequestHandler($requestHandler),
        $exceptionHandler
    );
    $executor->setLogger( new \Monolog\Logger('app',array( new \Monolog\Handler\StreamHandler(fopen('php://stdout', 'w')))) );
    $executor->execute(new \Syrma\WebContainer\ServerContext());

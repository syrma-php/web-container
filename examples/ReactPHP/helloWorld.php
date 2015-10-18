<?php

use Psr\Http\Message\RequestInterface;
use Syrma\WebContainer\Container;

require dirname(dirname(__DIR__)).'/vendor/autoload.php';

$container = new Container();
$container->setServer( $container->createReactServer());
$container->createExecutor(function(RequestInterface $request) use($container){

    $stream = $container->getPsr7Factory()->createStream();
    $stream->write('Hello World!');

    return $container->getPsr7Factory()->createResponse($stream);

})->execute();
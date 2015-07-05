<?php


namespace Syrma\WebContainer;

/**
 *
 */
interface ServerInterface {

    /**
     * Start the server
     *
     * @param ServerContextInterface $context
     * @param RequestHandlerInterface $requestHandler
     *
     * @return void
     */
    public function start( ServerContextInterface $context, RequestHandlerInterface $requestHandler );

    /**
     * Stop the server
     *
     * @return void
     */
    public function stop();

}
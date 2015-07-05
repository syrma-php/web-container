<?php


namespace Syrma\WebContainer;

/**
 * Context of server
 */
interface ServerContextInterface {

    const DEFAULT_ADDRESS = '0.0.0.0';
    const DEFAULT_PORT = 9100;

    /**
     * Address where the server will listen
     *
     * @example
     *      0.0.0.0   - all ip address
     *      10.0.0.12 - concrete ip address
     *
     * @return string
     */
    public function getListenAddress();

    /**
     * Port where the server will listen
     *
     * @example
     *      80 - < 1024 require root permission
     *      9100 - not required root permission
     *
     * @return int
     */
    public function getListenPort();


}
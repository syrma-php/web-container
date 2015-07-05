<?php


namespace Syrma\WebContainer;

/**
 * Default implementation of ServerContextInterface
 */
class ServerContext implements ServerContextInterface
{
    const EXT_CODE_ADDRESS_EMPTY   = 1;
    const EXT_CODE_ADDRESS_INVALID = 2;

    const EXT_CODE_PORT_EMPTY   = 3;
    const EXT_CODE_PORT_INVALID = 4;

    /**
     * @var string
     */
    private $listenAddress;

    /**
     * @var int
     */
    private $listenPort;

    /**
     * @param string $listenAddress
     * @param int $listenPort
     */
    public function __construct(
        $listenAddress = ServerContextInterface::DEFAULT_ADDRESS,
        $listenPort = ServerContextInterface::DEFAULT_PORT
    )
    {
        $this->listenAddress = $this->normalizeListenAddress($listenAddress);
        $this->listenPort    = $this->normalizeListenPort($listenPort);
    }

    /**
     * @param string $listenAddress
     *
     * @return string
     */
    private function normalizeListenAddress( $listenAddress )
    {

        if( empty($listenAddress) ){
            throw new \LogicException(
                'The listenAddress is empty!',
                self::EXT_CODE_ADDRESS_EMPTY
            );
        }

        // TODO - address validity check - socket, ipv4, ipv6

        return $listenAddress;
    }

    /**
     * @param string|int $listenPort
     *
     * @return int
     */
    private function normalizeListenPort( $listenPort )
    {
        if( empty($listenPort) ){
            throw new \LogicException(
                'The listenPort is empty!',
                self::EXT_CODE_PORT_EMPTY
            );
        }

        if( $listenPort != (string)intval($listenPort) ){
            throw new \InvalidArgumentException(sprintf(
                'The listen port [%s] is invalid, use the valid integer!',
                $listenPort
            ), self::EXT_CODE_PORT_INVALID);
        }

        if( $listenPort < 1 || $listenPort > 65535){
            throw new \OutOfRangeException(sprintf(
                'The listen port [%s] out of range (1-65535)',
                $listenPort
            ), self::EXT_CODE_PORT_INVALID);
        }

        return (int)$listenPort;
    }


    /**
     * {@inheritdoc}
     */
    public function getListenAddress()
    {
        return $this->listenAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function getListenPort()
    {
        return $this->listenPort;
    }
}
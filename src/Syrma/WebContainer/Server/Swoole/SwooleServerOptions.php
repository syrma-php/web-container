<?php

namespace Syrma\WebContainer\Server\Swoole;

/**
 * Validate and store options of Swoole server.
 */
class SwooleServerOptions
{
    const EXT_CODE_NOT_EXISTS = 1;
    const EXT_CODE_BAD_TYPE = 2;

    /**
     * @var array
     */
    private static $optionsConfig = array(
        'chroot' => 'string',
        'user' => 'string',
        'group' => 'string',
        'daemonize' => 'bool',
        'backlog' => 'int',
        'reactor_num' => 'int',
        'worker_num' => 'int',
        'discard_timeout_request' => 'bool',
        'enable_unsafe_event' => 'bool',
        'task_worker_num' => 'int',
        'task_ipc_mode' => 'int',
        'task_tmpdir' => 'string',
        'max_connection' => 'int',
        'max_request' => 'int',
        'task_max_request' => 'int',
        'open_cpu_affinity' => 'bool',
        'cpu_affinity_ignore' => 'array',
        'open_tcp_nodelay' => 'bool',
        'tcp_defer_accept' => 'int',
        'open_tcp_keepalive' => 'bool',
        'open_eof_split' => 'bool',
        'package_eof' => 'string',
        'open_http_protocol' => 'bool',
        'http_parse_post' => 'bool',
        'open_mqtt_protocol' => 'bool',
        'tcp_keepidle' => 'int',
        'tcp_keepinterval' => 'int',
        'tcp_keepcount' => 'int',
        'dispatch_mode' => 'int',
        'open_dispatch_key' => 'int',
        'dispatch_key_type' => 'string',
        'dispatch_key_offset' => 'int',
        'log_file' => 'string',
        'heartbeat_check_interval' => 'int',
        'heartbeat_idle_time' => 'int',
        'heartbeat_ping' => 'string',
        'heartbeat_pong' => 'string',
        'open_length_check' => 'bool',
        'package_length_offset' => 'int',
        'package_body_offset' => 'int',
        'package_max_length' => 'int',
        'buffer_input_size' => 'int',
        'buffer_output_size' => 'int',
        'pipe_buffer_size' => 'int',
        'message_queue_key' => 'int',
        'ssl_cert_file' => 'string',
        'ssl_key_file' => 'string',
    );

    /**
     * @var array
     */
    private $options = array();

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }
    }

    /**
     * Set option value.
     *
     * @param string                $name
     * @param array|string|bool|int $value
     *
     * @return self
     */
    public function setOption($name, $value)
    {
        $this->validate($name, $value);
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Get all server options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string                $name
     * @param array|string|bool|int $value
     */
    private function validate($name, $value)
    {
        if (!isset(self::$optionsConfig[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'The option(%s) not supported by Swoole server! Available options: %s',
                $name,
                implode(', ', array_keys(self::$optionsConfig))
            ), self::EXT_CODE_NOT_EXISTS);
        }

        $isValid = false;
        switch (self::$optionsConfig[$name]) {
            case 'int':
                $isValid = is_int($value);
                break;

            case 'bool':
                $isValid = is_bool($value);
                break;

            case 'string':
                $isValid = is_string($value);
                break;

            case 'array':
                $isValid = is_array($value);
                break;
        }

        if ($isValid !== true) {
            throw new \InvalidArgumentException(sprintf(
                'The option(%s) value(%s) is not %s, because it is %s!',
                $name,
                is_scalar($value) ? $value : 'object or array or resource',
                self::$optionsConfig[$name],
                gettype($value)
            ), self::EXT_CODE_BAD_TYPE);
        }
    }
}

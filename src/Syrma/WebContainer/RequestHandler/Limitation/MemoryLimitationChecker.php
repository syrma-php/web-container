<?php

namespace Syrma\WebContainer\RequestHandler\Limitation;

use Syrma\WebContainer\Exception\ServerStopException;

/**
 * Check the memory limit.
 *
 * @see http://php.net/manual/en/ini.core.php#ini.memory-limit
 *
 *  new MemoryLimitationChecker()       -> limit: php.ini value or unlimited
 *  new MemoryLimitationChecker(10)     -> limit: 10 bytes
 *  new MemoryLimitationChecker('10M')  -> limit: 10 485 760 bytes
 */
class MemoryLimitationChecker implements LimitationCheckerInterface
{
    /**
     * @var int
     */
    private $memoryLimit;

    /**
     * @var array
     */
    private static $metrics = array(
        'K' => 1024,
        'M' => 1048576, //1024*1024
        'G' => 1073741824, //1024*1024*1024
    );

    /**
     * @param int|string|null $memoryLimit
     */
    public function __construct($memoryLimit = null)
    {
        if (null === $memoryLimit) {
            if ('-1' === $iniValue = ini_get('memory_limit')) {
                $this->memoryLimit = PHP_INT_MAX;
            } else {
                $this->memoryLimit = self::parse($iniValue) - 10240; //minus 10KB for exception
            }
        } elseif (is_string($memoryLimit)) {
            $this->memoryLimit = self::parse($memoryLimit);
        } else {
            $this->memoryLimit = $memoryLimit;
        }
    }

    /**
     * @param string $limit
     *
     * @return float
     */
    private static function parse($limit)
    {
        $suffix = substr($limit, -1);
        if (isset(self::$metrics[$suffix])) {
            return floatval($limit) * self::$metrics[$suffix];
        } else {
            throw new \InvalidArgumentException(sprintf(
                'The limit(%s) not valid value! Valid values: 10K, 10M, 10G, or more',
                $limit
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkLimit()
    {
        if ($this->memoryLimit <= memory_get_usage(true)) {
            throw new ServerStopException(sprintf(
                'Out of memory! Usage: %d, limit: %d  ',
                memory_get_usage(true),
                $this->memoryLimit
            ));
        }
    }
}

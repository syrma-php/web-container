<?php

namespace Syrma\WebContainer\RequestHandler\Limitation;

use Syrma\WebContainer\Exception\ServerStopException;

/**
 * Check the execution time.
 *
 * @see http://php.net/manual/en/datetime.formats.relative.php
 *
 *  new ExecutionTimeLimitationChecker( 100 )                       -> the server will run  until 100 sec
 *  new ExecutionTimeLimitationChecker( strtotime('2015-12-24') )   -> the server will run until Christmas
 *
 *  new ExecutionTimeLimitationChecker( '+3hours' )     -> the server will run  until 3 hours
 *  new ExecutionTimeLimitationChecker( '2015-12-24' )  -> the server will run until Christmas
 */
class ExecutionTimeLimitationChecker implements LimitationCheckerInterface
{
    /**
     * @var int
     */
    private $willStop;

    /**
     * @param int|string $maxExecTime
     */
    public function __construct($maxExecTime)
    {
        if (is_int($maxExecTime)) {
            if ($maxExecTime > time()) { //absolute value
                $this->willStop = $maxExecTime;
            } else { //relative value
                $this->willStop = time() + $maxExecTime;
            }
        } else {
            $this->willStop = (new \DateTime($maxExecTime))->getTimestamp();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkLimit()
    {
        if ($this->willStop < time()) {
            throw new ServerStopException('The server is too old!');
        }
    }
}

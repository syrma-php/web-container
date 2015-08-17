<?php

namespace Syrma\WebContainer\RequestHandler\Limitation;

use Syrma\WebContainer\Exception\ServerStopExceptionInterface;

/**
 * Interface of the limit checker.
 */
interface LimitationCheckerInterface
{
    /**
     * Check limit.
     *
     * @throws ServerStopExceptionInterface - if overflow limit throw exception
     */
    public function checkLimit();
}

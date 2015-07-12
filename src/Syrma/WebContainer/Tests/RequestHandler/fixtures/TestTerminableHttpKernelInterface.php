<?php

namespace Syrma\WebContainer\Tests\RequestHandler\fixtures;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

/**
 * @see https://github.com/sebastianbergmann/phpunit-mock-objects/issues/213
 */
interface TestTerminableHttpKernelInterface extends HttpKernelInterface, TerminableInterface
{
}

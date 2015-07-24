<?php

namespace Syrma\WebContainer\Tests\Util;

use Syrma\WebContainer\Util\ErrorPageLoader;

class ErrorPageLoaderStub extends ErrorPageLoader
{
    protected function guessFile($statusCode)
    {
        return sys_get_temp_dir().'/not-exists.html';
    }
}

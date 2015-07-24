<?php

namespace Syrma\WebContainer\Tests\Server;

class ServerNotAvaiableStub extends ServerStub
{
    /**
     * {@inheritdoc}
     */
    public static function isAvaiable()
    {
        return false;
    }
}

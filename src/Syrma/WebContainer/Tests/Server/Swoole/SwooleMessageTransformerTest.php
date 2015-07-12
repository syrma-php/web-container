<?php

namespace Syrma\WebContainer\Tests\Server\Swoole;

class SwooleMessageTransformerTest extends AbstractSwooleMessageTransformerTest
{
    /**
     * {@inheritdoc}
     */
    protected function isUseServerRequest()
    {
        return false;
    }
}

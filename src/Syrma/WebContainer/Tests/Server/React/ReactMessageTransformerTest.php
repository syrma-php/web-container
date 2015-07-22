<?php

namespace Syrma\WebContainer\Tests\Server\React;

class ReactMessageTransformerTest extends AbstractReactMessageTransformerTest
{
    /**
     * {@inheritdoc}
     */
    protected function isUseServerRequest()
    {
        return false;
    }
}

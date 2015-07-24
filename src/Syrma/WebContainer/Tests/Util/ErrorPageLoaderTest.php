<?php

namespace Syrma\WebContainer\Tests\Util;

use Syrma\WebContainer\Util\ErrorPageLoader;

class ErrorPageLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadContentByStatusCode()
    {
        $loader = new ErrorPageLoader(__DIR__.'/fixtures/ErrorPage');

        try {
            $contentDefault = $loader->loadContentByStatusCode(0);
            $this->assertTrue(is_resource($contentDefault), 'The content is not resource');
            $this->assertEquals('<h1>error</h1>', stream_get_contents($contentDefault));
        } finally {
            fclose($contentDefault);
        }

        try {
            $content500 = $loader->loadContentByStatusCode(500);
            $this->assertTrue(is_resource($content500), 'The content is not resource');
            $this->assertEquals('<h1>500</h1>', stream_get_contents($content500));
        } finally {
            fclose($content500);
        }
    }

    public function testCreateDefault()
    {
        $loader = ErrorPageLoader::createDefault();

        try {
            $contentDefault = $loader->loadContentByStatusCode(0);
            $this->assertTrue(is_resource($contentDefault), 'The content is not resource');
            $this->assertContains('<h1>Oops! An Error Occurred</h1>', stream_get_contents($contentDefault));
        } finally {
            fclose($contentDefault);
        }
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1
     */
    public function testBadTemplatePath()
    {
        new ErrorPageLoader(sys_get_temp_dir());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionCode 2
     */
    public function testBadTemplateFile()
    {
        (new ErrorPageLoaderStub(__DIR__.'/fixtures/ErrorPage'))
            ->loadContentByStatusCode(100);
    }
}

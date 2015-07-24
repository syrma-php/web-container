<?php

namespace Syrma\WebContainer\Util;

/**
 * Load the error page content.
 *
 * search templates:
 *   - {templatePath}/{statusCode}.html
 *   - {templatePath}/error.html - default
 */
class ErrorPageLoader implements ErrorPageLoaderInterface
{
    const EXT_CODE_NOT_EXISTS = 1;
    const EXT_CODE_NOT_OPEN = 2;

    /**
     * @var string
     */
    private $templatePath;

    /**
     * @var string
     */
    private $defaultErrorFileName;

    /**
     * @param string $templatePath
     */
    public function __construct($templatePath)
    {
        $this->templatePath = $templatePath;
        $this->defaultErrorFileName = sprintf('%s/error.html', $this->templatePath);

        if (!is_readable($this->defaultErrorFileName)) {
            throw  new \RuntimeException(sprintf(
                'The error page template file(%s) not readable or not exists!',
                $this->defaultErrorFileName
            ), self::EXT_CODE_NOT_EXISTS);
        }
    }

    /**
     * @return ErrorPageLoader
     */
    public static function createDefault()
    {
        return new static(dirname(__DIR__).'/Resources/views/ErrorPage/');
    }

    /**
     * {@inheritdoc}
     */
    public function loadContentByStatusCode($statusCode)
    {
        $fileName = $this->guessFile($statusCode);

        if (false === $file = fopen($fileName, 'r')) {
            throw new \RuntimeException(sprintf('The file(%s) cannot open!'), self::EXT_CODE_NOT_OPEN);
        };

        $content = fopen('php://temp', 'r+');
        stream_copy_to_stream($file, $content);

        fclose($file);
        fseek($content, 0);

        return $content;
    }

    /**
     * @param int $statusCode
     *
     * @return string
     */
    private function guessFile($statusCode)
    {
        $fileName = sprintf('%s/%s.html', $this->templatePath, (int) $statusCode);

        if (is_readable($fileName)) {
            return $fileName;
        } else {
            return $this->defaultErrorFileName;
        }
    }
}

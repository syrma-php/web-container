<?php

namespace Syrma\WebContainer\Exception;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Syrma\WebContainer\ExceptionHandlerInterface;
use Syrma\WebContainer\Util\ErrorPageLoaderInterface;
use Syrma\WebContainer\Util\Psr7FactoryInterface;

/**
 * Default implementation of ExceptionHandlerInterface.
 */
class DefaultExceptionHandler implements ExceptionHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var Psr7FactoryInterface
     */
    private $messageFactory;

    /**
     * @var ErrorPageLoaderInterface
     */
    private $errorPageLoader;

    public function __construct(Psr7FactoryInterface $messageFactory, ErrorPageLoaderInterface $errorPageLoader)
    {
        $this->errorPageLoader = $errorPageLoader;
        $this->messageFactory = $messageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception)
    {
        $this->logException($exception);

        throw new ServerStopWithResponseException(
            $this->createErrorResponseByException($exception),
            $exception->getMessage(),
            $exception->getCode(),
            $exception
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createErrorResponseByException(\Exception $ex = null)
    {
        //TODO - handle more content types

        if (null !== $ex && $ex instanceof HttpExceptionInterface) {
            $statusCode = $ex->getStatusCode();
            $headers = (array) $ex->getHeaders();
        } else {
            $statusCode = 500;
            $headers = array(
                'Content-Type' => array('text/html; charset=UTF-8'),
                'Cache-Control' => array('max-age=0','must-revalidate','no-cache','no-store','private'),
            );
        }

        return $this->messageFactory->createResponse(
            $this->errorPageLoader->loadContentByStatusCode($statusCode),
            $statusCode,
            $headers
        );
    }

    /**
     * @param \Exception $exception
     */
    private function logException(\Exception $exception)
    {
        $message = sprintf(
            'Unexpected exception(%s) in server(pid: %s): %s',
            get_class($exception),
            posix_getpid(),
            $exception->getMessage()
        );

        if (null !== $this->logger) {
            $this->logger->critical($message, array('exception' => $exception));
        } else {
            error_log('[CRITICAL] - SyrmaWebContainer - '.$message);
        }
    }
}

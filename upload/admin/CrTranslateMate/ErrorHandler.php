<?php

namespace CrTranslateMate;

use Log;

class ErrorHandler
{
    /** @var Log */
    private $logger;
    private $isAjax;

    /**
     * @param Log $logger
     * @param bool|false $isAjax
     */
    public function __construct(Log $logger, $isAjax = false)
    {
        $this->logger = $logger;
        $this->isAjax = $isAjax;
        set_error_handler(array($this, 'handleError'));
        register_shutdown_function(array($this, 'handleShutdown'));
    }

    /**
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     */
    public function handleError($errno, $errstr, $errfile, $errline)
    {

        $this->logger->write($this->getErrorLabel($errno) . '"' . $errstr . '" in ' . $errfile . ' on line ' . $errline);

        if ($this->isAjax && $this->shouldKillProcess($errno)) {
            if (!headers_sent()) {
                header('HTTP/1.1 500 Internal Server Error');
            }
            $this->kill();
        }
    }

    public function handleShutdown()
    {
        if (is_null($e = error_get_last()) === false) {
            $this->logger->write(
                $this->getErrorLabel($e['type']) . '"' . $e['message'] . '" in ' . $e['file'] . ' on line ' . $e['line']
            );
            if ($this->shouldKillProcess($e['type'])) {
                if ($this->isAjax && !headers_sent()) {
                    header('HTTP/1.1 500 Internal Server Error');
                }
                $this->kill();
            }
        }
    }

    protected function kill()
    {
        die();
    }

    /**
     * @param int $errorType
     * @return string
     */
    protected function getErrorLabel($errorType)
    {
        switch ($errorType) {
            case E_NOTICE:
            case E_USER_NOTICE:
                return 'NOTICE: ';
            case E_WARNING:
            case E_USER_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_DEPRECATED:
            case E_DEPRECATED:
            case E_RECOVERABLE_ERROR:
                return 'WARNING: ';
            case E_PARSE :
                return "PARSE ERROR: ";
            case E_COMPILE_ERROR :
                return "COMPILE ERROR: ";
            case E_ERROR:
            case E_USER_ERROR:
            case E_CORE_ERROR:
                return 'FATAL ERROR: ';
            default :
                return 'ERROR: ';
        }
    }

    /**
     * @param int $errorType
     * @return bool
     */
    protected function shouldKillProcess($errorType)
    {
        return in_array($errorType, array(E_PARSE, E_COMPILE_ERROR, E_ERROR, E_USER_ERROR, E_CORE_ERROR));
    }
}
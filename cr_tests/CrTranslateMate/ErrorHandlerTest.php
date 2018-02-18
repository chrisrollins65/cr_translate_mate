<?php

namespace CrTranslateMate;

use Log;
use PHPUnit\Framework\TestCase;

/* OVERRIDING INTERNAL PHP FUNCTIONS */
$lastError = [];
function error_get_last()
{
    global $lastError;
    return $lastError;
}

function headers_sent()
{
    return false;
}

function header($message)
{
    // do nothing
}

function set_error_handler($errorHandlerMethod)
{
    // do nothing
}

function register_shutdown_function($errorHandlerMethod)
{
    // do nothing
}

/* END OVERRIDING INTERNAL PHP FUNCTIONS */

class ErrorHandlerTest extends TestCase
{
    /** @var Log | \PHPUnit_Framework_MockObject_MockObject */
    private $loggerMock;
    private $errstr = 'error string';
    private $errfile = 'error file';
    private $errline = '5';

    protected function setUp()
    {
        $this->loggerMock = $this->getMockBuilder(Log::class)
            ->disableOriginalConstructor()->setMethods(['write'])->getMock();
    }

    protected function tearDown()
    {
        global $lastError;
        $lastError = [];
    }

    public function testHandlingNotice()
    {
        $errno = E_NOTICE;
        $expectedLogMessage = $this->buildMessage('NOTICE');
        $this->loggerMock->expects($this->once())->method('write')->with($expectedLogMessage);
        $this->getErrorHandler()->handleError($errno, $this->errstr, $this->errfile, $this->errline);
    }

    public function testHandlingWarning()
    {
        $errno = E_WARNING;
        $expectedLogMessage = $this->buildMessage('WARNING');
        $this->loggerMock->expects($this->once())->method('write')->with($expectedLogMessage);
        $this->getErrorHandler()->handleError($errno, $this->errstr, $this->errfile, $this->errline);
    }

    public function testHandlingError()
    {
        $errno = E_ERROR;
        $expectedLogMessage = $this->buildMessage('FATAL ERROR');
        $this->loggerMock->expects($this->once())->method('write')->with($expectedLogMessage);
        $this->getErrorHandler()->handleError($errno, $this->errstr, $this->errfile, $this->errline);
    }

    public function testHandlingAnyOtherError()
    {
        $errno = 'Some other error type';
        $expectedLogMessage = $this->buildMessage('ERROR');
        $this->loggerMock->expects($this->once())->method('write')->with($expectedLogMessage);
        $this->getErrorHandler()->handleError($errno, $this->errstr, $this->errfile, $this->errline);
    }

    public function testHandlingAjaxErrors()
    {
        $errno = E_ERROR;
        $expectedLogMessage = $this->buildMessage('FATAL ERROR');
        $this->loggerMock->expects($this->once())->method('write')->with($expectedLogMessage);
        $errorHandler = $this->getErrorHandler(true, true);
        $errorHandler->expects($this->once())->method('kill');
        $errorHandler->handleError($errno, $this->errstr, $this->errfile, $this->errline);
    }

    public function testHandlingParseError()
    {
        $this->setLastError(E_PARSE);
        $expectedLogMessage = $this->buildMessage('PARSE ERROR');
        $this->loggerMock->expects($this->once())->method('write')->with($expectedLogMessage);
        $errorHandler = $this->getErrorHandler(true);
        $errorHandler->expects($this->once())->method('kill');
        $errorHandler->handleShutdown();
    }

    public function testHandlingCompileError()
    {
        $this->setLastError(E_COMPILE_ERROR);
        $expectedLogMessage = $this->buildMessage('COMPILE ERROR');
        $this->loggerMock->expects($this->once())->method('write')->with($expectedLogMessage);
        $errorHandler = $this->getErrorHandler(true);
        $errorHandler->expects($this->once())->method('kill');
        $errorHandler->handleShutdown();
    }

    public function testHandlingAjaxShutdownError()
    {
        $this->setLastError(E_ERROR);
        $expectedLogMessage = $this->buildMessage('FATAL ERROR');
        $this->loggerMock->expects($this->once())->method('write')->with($expectedLogMessage);
        $errorHandler = $this->getErrorHandler(true, true);
        $errorHandler->expects($this->once())->method('kill');
        $errorHandler->handleShutdown();
    }

    /**
     * @param bool|false $mock
     * @param bool|false $isAjax
     * @return ErrorHandler|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getErrorHandler($mock = false, $isAjax = false)
    {
        return $mock
            ? $this->getMockBuilder(ErrorHandler::class)
                ->setConstructorArgs(['logger' => $this->loggerMock, 'isAjax' => $isAjax])
                ->setMethods(['kill'])->getMock()
            : new ErrorHandler($this->loggerMock, $isAjax);
    }

    /**
     * @param string $label
     * @return string
     */
    private function buildMessage($label)
    {
        return $label . ': "' . $this->errstr . '" in ' . $this->errfile . ' on line ' . $this->errline;
    }

    /**
     * @param int $type
     */
    private function setLastError($type)
    {
        global $lastError;
        $lastError = [
            'type' => $type,
            'message' => $this->errstr,
            'file' => $this->errfile,
            'line' => $this->errline,
        ];
    }
}
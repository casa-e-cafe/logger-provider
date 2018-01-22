<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 14/12/17
 */

namespace CasaCafe\Library\Logger;

use Psr\Log\LoggerInterface;
use Monolog\Logger as MonologLogger;

class Logger implements LoggerInterface
{
    static private $instance;

    /** @var  MonologLogger */
    private $vendorLogger;

    private function __construct()
    {
    }

    public function setVendorLogger(MonologLogger $vendorLogger)
    {
        $this->vendorLogger = $vendorLogger;
    }

    public static function getInstance() : Logger
    {
        if (is_null(self::$instance)) {
            self::$instance = new Logger();
        }

        return self::$instance;
    }

    public function __call($name, $arguments)
    {
        call_user_func_array([$this->vendorLogger, $name], $arguments);
    }

    public function isHandling($level)
    {
        return $this->vendorLogger->isHandling($level);
    }

    public function logException(\Exception $exception, $message = '', $context = [])
    {
        $exceptionMessage = $exception->getMessage();
        $infoMessage = empty($message) ?
            sprintf('Exception: %s', $message):
            sprintf('%s - Exception: %s', $message, $exceptionMessage);

        $this->vendorLogger->info($infoMessage, $context);

        $debugContext = $context;
        $debugContext[] = $exception->getTraceAsString();
        $this->vendorLogger->debug($exceptionMessage, $debugContext);
    }

    public function emergency($message, array $context = array())
    {
        $this->vendorLogger->emergency($message, $context);
    }

    public function alert($message, array $context = array())
    {
        $this->vendorLogger->alert($message, $context);
    }

    public function critical($message, array $context = array())
    {
        $this->vendorLogger->critical($message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->vendorLogger->error($message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->vendorLogger->warning($message, $context);
    }

    public function notice($message, array $context = array())
    {
        $this->vendorLogger->notice($message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->vendorLogger->info($message, $context);
    }

    public function debug($message, array $context = array())
    {
        $this->vendorLogger->debug($message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        $this->vendorLogger->log($level, $message, $context);
    }
}

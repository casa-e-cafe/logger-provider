<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 14/12/17
 */

namespace CasaCafe\Library\Logger;


use Psr\Log\LoggerInterface;

class Logger
{
    static private $instance;

    /** @var  LoggerInterface */
    private $vendorLogger;

    private function __construct()
    {
    }

    public function setVendorLogger(LoggerInterface $vendorLogger)
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
}
<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 14/12/17
 */

namespace CasaCafe\Library\Logger\Provider;

use CasaCafe\Library\Logger\Logger;
use CasaCafe\Library\Logger\Processor\RecordLogProcessor;
use CasaCafe\Library\Logger\Processor\SensitiveArrayProcessor;
use CasaCafe\Library\Logger\Processor\SensitiveStringProcessor;
use Monolog\Logger as MonologLogger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Provider\MonologServiceProvider;

class LoggerProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple->register(new MonologServiceProvider(), $pimple['monolog']);

        $pimple['log-processor'] = function (Container $pimple) {
            return new RecordLogProcessor(
                $pimple['message-log-processor'],
                $pimple['context-log-processor']
            );
        };

        $pimple['message-log-processor'] = function (Container $pimple) {
            return new SensitiveStringProcessor($pimple['log-processor-cfg-validated']);
        };

        $pimple['context-log-processor'] = function (Container $pimple) {
            return new SensitiveArrayProcessor($pimple['log-processor-cfg-validated']);
        };

        $pimple['log-processor-cfg-validated'] = function (Container $pimple) {
            $logProcessorConfig = [];
            if (isset($pimple['log-processor-configuration'])) {
                $logProcessorConfig = $pimple['log-processor-configuration'];
            }
            return $logProcessorConfig;
        };

        $pimple['log-processor-function'] = function (Container $pimple) {

            /** @var RecordLogProcessor $logProcessor */
            $logProcessor = $pimple['log-processor'];
            return $logProcessor->getProcessorFunction();
        };

        /** @var MonologLogger $monolog */
        $monolog = $pimple['monolog'];
        $monolog->pushProcessor($pimple['log-processor-function']);

        Logger::getInstance()->setVendorLogger($pimple['monolog']);
    }
}
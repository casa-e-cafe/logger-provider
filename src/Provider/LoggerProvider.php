<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 14/12/17
 */

namespace CasaCafe\Library\Logger\Provider;


use Monolog\Logger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Provider\MonologServiceProvider;

class LoggerProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple->register(new MonologServiceProvider(), $pimple['monolog']);

        /** @var Logger $monolog */
        $monolog = $pimple['monolog'];
        $monolog->pushProcessor(
            function ($record) {
                $logErrorMsg = '[** This log message could contain sensitive information, so it was removed **]';
                $passRegex = '/.*(pass|senha).*/';

                $context = $record['context'];
                if (preg_match($passRegex, strtolower($record['message']))) {
                    $record['message'] = $logErrorMsg;
                }

                array_walk_recursive(
                    $context,
                    function (&$value, $key, $passRegex) {
                        if (preg_match($passRegex, strtolower($key))) {
                            $value = '**********';
                        }
                    },
                    $passRegex
                );

                $record['context'] = $context;

                return $record;
            }
        );
    }
}
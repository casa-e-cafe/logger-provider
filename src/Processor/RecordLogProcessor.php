<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 15/12/17
 */

namespace CasaCafe\Library\Logger\Processor;


class RecordLogProcessor
{

    private $messageProcessor;
    private $contextProcessor;

    public function __construct(array $config = []) {

        $this->messageProcessor = new SensitiveStringProcessor($config);
        $this->contextProcessor = new SensitiveArrayProcessor($config);
    }

    public function getProcessorFunction() : callable {
        return function ($record) {

            if (isset( $record['message'])) {
                $record['message'] = $this->messageProcessor->replaceMessageSensitiveString($record['message']);
            }

            if (isset( $record['context'])) {
                $arrayContext = json_decode(json_encode($record['context']),TRUE);
                $record['context'] = $this->contextProcessor->replaceArraySensitiveInfo($arrayContext);
            }

            return $record;
        };
    }
}
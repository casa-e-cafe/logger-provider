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

    public function __construct(
        SensitiveInfoProcessorInterface $messageProcessor,
        SensitiveInfoProcessorInterface $contextProcessor
    ) {

        $this->messageProcessor = $messageProcessor;
        $this->contextProcessor = $contextProcessor;
    }

    public function processLog($record)
    {
        if (isset($record['message'])) {
            $record['message'] = $this->messageProcessor->replaceSensitiveInfo($record['message']);
        }

        if (isset($record['context'])) {
            $arrayContext = json_decode(json_encode($record['context']), true);
            $record['context'] = $this->contextProcessor->replaceSensitiveInfo($arrayContext);
        }
        return $record;
    }
}

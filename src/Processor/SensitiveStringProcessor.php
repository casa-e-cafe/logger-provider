<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 18/12/17
 */

namespace CasaCafe\Library\Logger\Processor;


use CasaCafe\Library\Logger\Processor\Util\InfoReplacementTrait;

class SensitiveStringProcessor implements SensitiveInfoProcessorInterface
{
    use InfoReplacementTrait;

    public function __construct(array $config = []) {

        $internalConfig = [
            'word-regex-default' => 'senha|pass',
            'word-regex-key' => 'processor-regex',
            'replacement-default' => '[** This log message could contain sensitive information, so it was removed **]',
            'replacement-key' => 'string-replacement'
        ];

        $this->setupProcessor($config, $internalConfig);
    }

    public function replaceSensitiveInfo($message) : string {

        if ($this->isSensitiveString($message)) {
            $message = $this->replacementString;
        }
        return $message;
    }
}
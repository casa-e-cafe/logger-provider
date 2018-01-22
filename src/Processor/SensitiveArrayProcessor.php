<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 18/12/17
 */

namespace CasaCafe\Library\Logger\Processor;

use CasaCafe\Library\Logger\Processor\Util\InfoReplacementTrait;

class SensitiveArrayProcessor implements SensitiveInfoProcessorInterface
{
    use InfoReplacementTrait;

    public function __construct(array $config = [])
    {
        $internalConfig = [
            'word-regex-default' => 'senha|pass',
            'word-regex-key' => 'processor-regex',
            'replacement-default' => '**********',
            'replacement-key' => 'context-replacement'
        ];

        $this->setupProcessor($config, $internalConfig);
    }

    public function replaceSensitiveInfo($context) : array
    {
        array_walk_recursive(
            $context,
            function (&$value, $key) {
                if ($this->isSensitiveString($key)) {
                    $value = $this->replacementString;
                }
            }
        );

        return $context;
    }
}

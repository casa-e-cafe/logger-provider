<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 19/12/17
 */

namespace CasaCafe\Library\Logger\Processor\Util;

trait InfoReplacementTrait
{
    private $sensitiveWordsRegex;
    private $replacementString;
    private $internalConfig;

    private function setupProcessor($externalConfig, $internalConfig)
    {
        $this->internalConfig = $internalConfig;
        $this->setupSensitiveWordsRegex($externalConfig);
        $this->setupReplacementString($externalConfig);
    }

    private function setupSensitiveWordsRegex($config)
    {
        $regexString = $this->getConfigValueIfExists(
            $config,
            $this->internalConfig['word-regex-key'],
            $this->internalConfig['word-regex-default']
        );

        $regex = sprintf('/%s/', $regexString);

        $this->sensitiveWordsRegex = $regex;
    }

    private function setupReplacementString($config)
    {
        $this->replacementString = $this->getConfigValueIfExists(
            $config,
            $this->internalConfig['replacement-key'],
            $this->internalConfig['replacement-default']
        );
    }

    private function getConfigValueIfExists($config, $configKey, $defaultValue) : string
    {
        $value = $defaultValue;

        if (isset($config[$configKey])) {
            $value = $config[$configKey];
        }

        return $value;
    }

    private function isSensitiveString($value)
    {
        return preg_match($this->sensitiveWordsRegex, $value);
    }
}

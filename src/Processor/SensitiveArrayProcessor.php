<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 18/12/17
 */

namespace CasaCafe\Library\Logger\Processor;


class SensitiveArrayProcessor
{
    CONST DEFAULT_SENSITIVE_WORDS_REGEX = '/senha|pass/';
    CONST DEFAULT_CTX_REPLACEMENT = '**********';

    private $sensitiveWordsRegex;
    private $contextReplacement;

    public function __construct(array $config = []) {
        $this->setupSensitiveWordsRegex($config);
        $this->setupArrayReplacementValue($config);

    }

    public function replaceArraySensitiveInfo(array $context) : array {

        array_walk_recursive(
            $context,
            function (&$value, $key) {
                if($this->isSensitiveString($key)) {
                    $value = $this->contextReplacement;
                }
            }
        );

        return $context;
    }

    private function setupSensitiveWordsRegex($config) {

        $this->sensitiveWordsRegex = self::DEFAULT_SENSITIVE_WORDS_REGEX;

        if (isset($config['processor-regex'])) {
            $customRegex = sprintf('/%s/', $config['processor-regex']);
            $this->sensitiveWordsRegex = $customRegex;
        }
    }

    private function setupArrayReplacementValue($config) {

        $this->contextReplacement = self::DEFAULT_CTX_REPLACEMENT;

        if (isset($config['context-replacement'])) {
            $this->contextReplacement = $config['context-replacement'];
        }
    }

    private function isSensitiveString($value) {
        return preg_match($this->sensitiveWordsRegex, $value);
    }
}
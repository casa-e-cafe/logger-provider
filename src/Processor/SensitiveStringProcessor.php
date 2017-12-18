<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 18/12/17
 */

namespace CasaCafe\Library\Logger\Processor;


class SensitiveStringProcessor
{
    CONST DEFAULT_SENSITIVE_WORDS_REGEX = '/senha|pass/';
    CONST DEFAULT_STR_REPLACEMENT = '[** This log message could contain sensitive information, so it was removed **]';

    private $sensitiveWordsRegex;
    private $stringReplacement;

    public function __construct(array $config = []) {
        $this->setupSensitiveWordsRegex($config);
        $this->setupStringReplacementMessage($config);
    }

    public function replaceMessageSensitiveString(string $message) : string {

        if ($this->isSensitiveString($message)) {
            $message = $this->stringReplacement;
        }
        return $message;
    }

    private function setupSensitiveWordsRegex($config) {

        $this->sensitiveWordsRegex = self::DEFAULT_SENSITIVE_WORDS_REGEX;

        if (isset($config['processor-regex'])) {
            $customRegex = sprintf('/%s/', $config['processor-regex']);
            $this->sensitiveWordsRegex = $customRegex;
        }
    }

    private function setupStringReplacementMessage($config) {

        $this->stringReplacement = self::DEFAULT_STR_REPLACEMENT;

        if (isset($config['string-replacement'])) {
            $this->stringReplacement = $config['string-replacement'];
        }
    }

    private function isSensitiveString($value) {
        return preg_match($this->sensitiveWordsRegex, $value);
    }
}
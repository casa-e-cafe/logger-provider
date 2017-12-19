<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 18/12/17
 */

namespace CasaCafe\Tests\Library\Logger\Processor;


use CasaCafe\Library\Logger\Processor\SensitiveStringProcessor;
use PHPUnit\Framework\TestCase;

class SensitiveStringProcessorTest extends TestCase
{
    CONST STRING_REPLACED = '[** This log message could contain sensitive information, so it was removed **]';

    public function testDoNotReplaceString() {
        $processor = new SensitiveStringProcessor();
        $replacedString = $processor->replaceSensitiveInfo('abracadabra');
        $this->assertEquals('abracadabra', $replacedString);
    }

    public function testReplaceSenhaString() {
        $processor = new SensitiveStringProcessor();
        $replacedString = $processor->replaceSensitiveInfo('senha');
        $this->assertEquals(self::STRING_REPLACED, $replacedString);
    }

    public function testReplacePassString() {
        $processor = new SensitiveStringProcessor();
        $replacedString = $processor->replaceSensitiveInfo('pass');
        $this->assertEquals(self::STRING_REPLACED, $replacedString);
    }

    public function testReplaceComplexString() {
        $processor = new SensitiveStringProcessor();
        $replacedString = $processor->replaceSensitiveInfo('should not see this pass message');
        $this->assertEquals(self::STRING_REPLACED, $replacedString);
    }

    public function testDoNotReplaceComplexString() {
        $processor = new SensitiveStringProcessor();
        $replacedString = $processor->replaceSensitiveInfo('should see this message');
        $this->assertEquals('should see this message', $replacedString);
    }

    public function testConfigurableRegexTest() {

        $message = 'tem que ver se tem samba';
        $config = ['processor-regex' => 'samba'];

        $processor = new SensitiveStringProcessor($config);
        $replacedString = $processor->replaceSensitiveInfo($message);

        $this->assertEquals(self::STRING_REPLACED, $replacedString);
    }

    public function testConfigurableStringReplacement() {

        $replacementString = '[** HIDDEN **]';
        $config = ['string-replacement' => $replacementString];
        $message = 'tem que ver se tem senha';

        $processor = new SensitiveStringProcessor($config);
        $replacedString = $processor->replaceSensitiveInfo($message);

        $this->assertEquals($replacementString, $replacedString);
    }
}
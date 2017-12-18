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
        $replacedString = $processor->replaceMessageSensitiveString('abracadabra');
        $this->assertEquals('abracadabra', $replacedString);
    }

    public function testReplaceSenhaString() {
        $processor = new SensitiveStringProcessor();
        $replacedString = $processor->replaceMessageSensitiveString('senha');
        $this->assertEquals(self::STRING_REPLACED, $replacedString);
    }

    public function testReplacePassString() {
        $processor = new SensitiveStringProcessor();
        $replacedString = $processor->replaceMessageSensitiveString('pass');
        $this->assertEquals(self::STRING_REPLACED, $replacedString);
    }

    public function testReplaceComplexString() {
        $processor = new SensitiveStringProcessor();
        $replacedString = $processor->replaceMessageSensitiveString('should not see this pass message');
        $this->assertEquals(self::STRING_REPLACED, $replacedString);
    }

    public function testDoNotReplaceComplexString() {
        $processor = new SensitiveStringProcessor();
        $replacedString = $processor->replaceMessageSensitiveString('should see this message');
        $this->assertEquals('should see this message', $replacedString);
    }

}
<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 18/12/17
 */

namespace CasaCafe\Tests\Library\Logger\Processor;

use CasaCafe\Library\Logger\Processor\SensitiveArrayProcessor;
use PHPUnit\Framework\TestCase;

class SensitiveArrayProcessorTest extends TestCase
{

    public function testDoNotReplaceContextArrayInfo()
    {

        $processor = new SensitiveArrayProcessor();
        $initialContext = ['batata' => 'frita'];
        $changedContext = $processor->replaceSensitiveInfo($initialContext);
        $this->assertEquals($initialContext, $changedContext);
    }

    public function testReplaceContextArraySentiveInfo()
    {

        $processor = new SensitiveArrayProcessor();
        $initialContext = ['batata' => 'frita', 'senha' => 'nao deve ser exibida'];
        $changedContext = $processor->replaceSensitiveInfo($initialContext);

        $expectedContext = ['batata' => 'frita', 'senha' => '**********'];
        $this->assertEquals($expectedContext, $changedContext);
    }

    public function testReplaceContextArrayMultipleSentiveInfos()
    {

        $processor = new SensitiveArrayProcessor();
        $initialContext = [
            'batata' => 'frita',
            'senha' => 'nao deve ser exibida',
            'outros' => [
                'teste' => 'bora',
                'pass' => 'tambem nao deve ser exibida'
            ]
        ];
        $changedContext = $processor->replaceSensitiveInfo($initialContext);

        $expectedContext =
        $initialContext = [
            'batata' => 'frita',
            'senha' => '**********',
            'outros' => [
                'teste' => 'bora',
                'pass' => '**********'
            ]
        ];
        $this->assertEquals($expectedContext, $changedContext);
    }

    public function testConfigurableRegexTest()
    {
        $context = [
            'batata' => 'frita',
            'senha' => 'deve ser exibida',
            'outros' => [
                'teste' => 'bora',
                'samba' => 'tambem nao deve ser exibida'
            ]
        ];

        $expectedContext = [
            'batata' => 'frita',
            'senha' => 'deve ser exibida',
            'outros' => [
                'teste' => 'bora',
                'samba' => '**********'
            ]
        ];

        $config = ['processor-regex' => 'samba'];

        $processor = new SensitiveArrayProcessor($config);
        $replacedContext = $processor->replaceSensitiveInfo($context);

        $this->assertEquals($expectedContext, $replacedContext);
    }

    public function testConfigurableContextReplacement()
    {
        $replacementString = '[** HIDDEN **]';

        $context = ['pass' =>  '123Catorze'];
        $config = ['context-replacement' => $replacementString];

        $processor = new SensitiveArrayProcessor($config);
        $replacedContext = $processor->replaceSensitiveInfo($context);

        $expectedContext = ['pass' =>  $replacementString];
        $this->assertEquals($expectedContext, $replacedContext);
    }
}

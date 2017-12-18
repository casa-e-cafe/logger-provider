<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 15/12/17
 */

namespace CasaCafe\Tests\Library\Logger\Processor;

use CasaCafe\Library\Logger\Processor\RecordLogProcessor;
use PHPUnit\Framework\TestCase;

class RecordLogProcessorTest extends TestCase
{

    public function testPasswordProcessorExists() {
        $processor = new RecordLogProcessor();
        $this->assertInstanceOf('CasaCafe\Library\Logger\Processor\RecordLogProcessor', $processor);
    }

    public function testProcessorFunctionIsAFunction() {
        $processor = new RecordLogProcessor();
        $function = $processor->getProcessorFunction();
        $this->assertInternalType("callable", $function);
    }

    public function testProcessorFunctionWithOnlyMessageContext() {
        $processor = new RecordLogProcessor();
        $function = $processor->getProcessorFunction();

        $record = ['message' => 'blah'];
        $processedRecord = $function($record);

        $this->assertEquals($record, $processedRecord);
    }

    public function testProcessorFunctionWithOnlyContextContext() {

        $processor = new RecordLogProcessor();
        $function = $processor->getProcessorFunction();

        $record = ['context' => ['alguma' => 'coisa']];
        $processedRecord = $function($record);

        $this->assertEquals($record, $processedRecord);
    }

    public function testProcessorFunctionWithSensitiveInfo() {

        $processor = new RecordLogProcessor();
        $function = $processor->getProcessorFunction();

        $record = [
            'message' => 'tem que ver se tem senha',
            'context' => [
                'batata' => 'frita',
                'senha' => 'nao deve ser exibida',
                'outros' => [
                    'teste' => 'bora',
                    'pass' => 'tambem nao deve ser exibida'
                ]
            ]
        ];

        $processedRecord = $function($record);

        $expectedRecord = [
            'message' => '[** This log message could contain sensitive information, so it was removed **]',
            'context' => [
                'batata' => 'frita',
                'senha' => '**********',
                'outros' => [
                    'teste' => 'bora',
                    'pass' => '**********'
                ]
            ]
        ];

        $this->assertEquals($expectedRecord, $processedRecord);
    }


    public function testStdClassContextArraySentiveInfo() {
        $processor = new RecordLogProcessor();
        $function = $processor->getProcessorFunction();

        $initialContext = new \stdClass();
        $initialContext->batata = 'frita';
        $initialContext->senha = 'nao deve ser exibida';
        $initialContext->outros = new \stdClass();
        $initialContext->outros->teste = 'bora';
        $initialContext->outros->pass = 'tambem nao deve ser exibida';

        $record = [
            'message' => 'tem que ver se tem senha',
            'context' => $initialContext
        ];


        $processedRecord = $function($record);
        $expectedRecord = [
            'message' => '[** This log message could contain sensitive information, so it was removed **]',
            'context' => [
                'batata' => 'frita',
                'senha' => '**********',
                'outros' => [
                    'teste' => 'bora',
                    'pass' => '**********'
                ]
            ]
        ];

        $this->assertEquals($expectedRecord, $processedRecord);
    }

    public function testProcessorFunctionWithNullMessage() {

        $processor = new RecordLogProcessor();
        $function = $processor->getProcessorFunction();

        $record = ['message' => null, 'context' => ['alguma' => 'coisa']];
        $processedRecord = $function($record);

        $this->assertEquals($record, $processedRecord);
    }

    public function testConfigurableRegexTest() {

        $config = ['processor-regex' => 'samba'];
        $processor = new RecordLogProcessor($config);
        $function = $processor->getProcessorFunction();

        $record = [
            'message' => 'tem que ver se tem samba',
            'context' => [
                'batata' => 'frita',
                'senha' => 'deve ser exibida',
                'outros' => [
                    'teste' => 'bora',
                    'samba' => 'tambem nao deve ser exibida'
                ]
            ]
        ];

        $processedRecord = $function($record);

        $expectedRecord = [
            'message' => '[** This log message could contain sensitive information, so it was removed **]',
            'context' => [
                'batata' => 'frita',
                'senha' => 'deve ser exibida',
                'outros' => [
                    'teste' => 'bora',
                    'samba' => '**********'
                ]
            ]
        ];

        $this->assertEquals($expectedRecord, $processedRecord);
    }

    public function testConfigurableStringReplacement() {

        $config = ['string-replacement' => '[** HIDDEN **]'];
        $processor = new RecordLogProcessor($config);
        $function = $processor->getProcessorFunction();
        $record = [
            'message' => 'tem que ver se tem senha',
            'context' => [ ]
        ];
        $processedRecord = $function($record);
        $expectedRecord = [
            'message' => '[** HIDDEN **]',
            'context' => [ ]
        ];

        $this->assertEquals($expectedRecord, $processedRecord);
    }

    public function testConfigurableContextReplacement() {

        $config = ['context-replacement' => '[** HIDDEN **]'];
        $processor = new RecordLogProcessor($config);
        $function = $processor->getProcessorFunction();
        $record = [
            'message' => 'oi, tudo bem?',
            'context' => ['pass' =>  '123Catorze']
        ];
        $processedRecord = $function($record);
        $expectedRecord = [
            'message' => 'oi, tudo bem?',
            'context' => ['pass' =>  '[** HIDDEN **]']
        ];

        $this->assertEquals($expectedRecord, $processedRecord);
    }
}
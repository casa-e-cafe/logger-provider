<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 15/12/17
 */

namespace CasaCafe\Tests\Library\Logger\Processor;

use CasaCafe\Library\Logger\Processor\RecordLogProcessor;
use CasaCafe\Library\Logger\Processor\SensitiveArrayProcessor;
use CasaCafe\Library\Logger\Processor\SensitiveStringProcessor;
use PHPUnit\Framework\TestCase;

class RecordLogProcessorTest extends TestCase
{

    public function testPasswordProcessorExists() {

        $messageProcessorMock = $this->getMessageProcessorMock();
        $contextProcessorMock = $this->getContextProcessorMock();
        $processor = new RecordLogProcessor($messageProcessorMock, $contextProcessorMock);
        $this->assertInstanceOf('CasaCafe\Library\Logger\Processor\RecordLogProcessor', $processor);
    }

    public function testProcessorFunctionIsAFunction() {

        $messageProcessorMock = $this->getMessageProcessorMock();
        $contextProcessorMock = $this->getContextProcessorMock();
        $processor = new RecordLogProcessor($messageProcessorMock, $contextProcessorMock);
        $function = $processor->getProcessorFunction();
        $this->assertInternalType("callable", $function);
    }

    public function testOnlyMessageRecordShouldCallMessageProcessor() {

        $recordMessage = 'blah';
        $replacedMessage = 'aoisjdfoijasd';

        $messageProcessorMock = $this->getMessageProcessorMock();
        $messageProcessorMock->expects($this->once())
            ->method('replaceSensitiveInfo')
            ->with($recordMessage)
            ->willReturn($replacedMessage);

        $contextProcessorMock = $this->getContextProcessorMock();
        $contextProcessorMock->expects($this->never())
            ->method('replaceSensitiveInfo');

        $processor = new RecordLogProcessor($messageProcessorMock, $contextProcessorMock);
        $function = $processor->getProcessorFunction();

        $record = ['message' => $recordMessage];
        $processedRecord = $function($record);

        $expectedRecord = ['message' => $replacedMessage];
        $this->assertEquals($expectedRecord, $processedRecord);
    }

    public function testOnlyContextRecordShouldCallContextProcessor() {

        $recordContext = ['alguma' => 'coisa'];
        $replacedRecordContext = ['alguma' => 'showaeihraosjdfasd'];
        $messageProcessorMock = $this->getMessageProcessorMock();
        $messageProcessorMock->expects($this->never())
            ->method('replaceSensitiveInfo');

        $contextProcessorMock = $this->getContextProcessorMock();
        $contextProcessorMock->expects($this->once())
            ->method('replaceSensitiveInfo')
            ->with($recordContext)
            ->willReturn($replacedRecordContext);

        $processor = new RecordLogProcessor($messageProcessorMock, $contextProcessorMock);
        $function = $processor->getProcessorFunction();

        $record = ['context' => $recordContext];
        $processedRecord = $function($record);

        $expectedRecord = ['context' => $replacedRecordContext];
        $this->assertEquals($expectedRecord, $processedRecord);
    }

    public function testFullRecordShouldCallBothProcessors() {

        $recordMessage = 'blah';
        $replacedRecordMessage = 'ajsdofjaoisjd';
        $recordContext = ['alguma' => 'coisa'];
        $replacedRecordContext = ['alguma' => 'pessoa'];
        $record = ['message' => $recordMessage, 'context' => $recordContext];

        $messageProcessorMock = $this->getMessageProcessorMock();
        $messageProcessorMock->expects($this->once())
            ->method('replaceSensitiveInfo')
            ->with($recordMessage)
            ->willReturn($replacedRecordMessage);

        $contextProcessorMock = $this->getContextProcessorMock();
        $contextProcessorMock->expects($this->once())
            ->method('replaceSensitiveInfo')
            ->with($recordContext)
            ->willReturn($replacedRecordContext);

        $processor = new RecordLogProcessor($messageProcessorMock, $contextProcessorMock);
        $function = $processor->getProcessorFunction();

        $processedRecord = $function($record);
        $expectedRecord = ['message' => $replacedRecordMessage, 'context' => $replacedRecordContext];
        $this->assertEquals($expectedRecord, $processedRecord);
    }

    public function testEmptyRecordShouldCallNoneProcessor() {

        $record = [];

        $messageProcessorMock = $this->getMessageProcessorMock();
        $messageProcessorMock->expects($this->never())
            ->method('replaceSensitiveInfo');

        $contextProcessorMock = $this->getContextProcessorMock();
        $contextProcessorMock->expects($this->never())
            ->method('replaceSensitiveInfo');

        $processor = new RecordLogProcessor($messageProcessorMock, $contextProcessorMock);
        $function = $processor->getProcessorFunction();

        $processedRecord = $function($record);

        $this->assertEquals($record, $processedRecord);
    }

    public function testStdClassContextShouldCallContextProcessor() {
        $initialContext = new \stdClass();
        $initialContext->batata = 'frita';
        $initialContext->chave = 'valor';
        $initialContext->outros = new \stdClass();
        $initialContext->outros->teste = 'bora';

        $record = [
            'context' => $initialContext
        ];

        $initialContextArray = [
            'batata' => 'frita',
            'chave' => 'valor',
            'outros' => [
                'teste' => 'bora'
            ]
        ];

        $messageProcessorMock = $this->getMessageProcessorMock();

        $contextProcessorMock = $this->getContextProcessorMock();
        $contextProcessorMock->expects($this->once())
            ->method('replaceSensitiveInfo')
            ->with($initialContextArray)
            ->willReturn($initialContextArray);

        $processor = new RecordLogProcessor($messageProcessorMock, $contextProcessorMock);

        $function = $processor->getProcessorFunction();
        $processedRecord = $function($record);

        $expectedRecord = ['context' => $initialContextArray];
        $this->assertEquals($expectedRecord, $processedRecord);
    }

    public function testProcessorFunctionWithNullMessage() {

        $messageProcessorMock = $this->getMessageProcessorMock();
        $messageProcessorMock->expects($this->never())
            ->method('replaceSensitiveInfo');
        $contextProcessorMock = $this->getContextProcessorMock();
        $processor = new RecordLogProcessor($messageProcessorMock, $contextProcessorMock);

        $function = $processor->getProcessorFunction();

        $record = ['message' => null];
        $processedRecord = $function($record);

        $this->assertEquals($record, $processedRecord);
    }

    private function getContextProcessorMock() {

        /** @var \PHPUnit_Framework_MockObject_MockObject|SensitiveArrayProcessor $messageProcessorMock */
        $messageProcessorMock = $this->getMockBuilder(SensitiveArrayProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $messageProcessorMock;
    }

    private function getMessageProcessorMock() {

        /** @var \PHPUnit_Framework_MockObject_MockObject|SensitiveStringProcessor $messageProcessorMock */
        $messageProcessorMock = $this->getMockBuilder(SensitiveStringProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $messageProcessorMock;
    }
}
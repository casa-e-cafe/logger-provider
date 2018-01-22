<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 19/12/17
 */

namespace CasaCafe\Tests\Library\Logger\Provider;

use CasaCafe\Library\Logger\Logger;
use CasaCafe\Library\Logger\Processor\RecordLogProcessor;
use CasaCafe\Library\Logger\Processor\SensitiveArrayProcessor;
use CasaCafe\Library\Logger\Processor\SensitiveStringProcessor;
use PHPUnit\Framework\TestCase;
use CasaCafe\Library\Logger\Provider\LoggerProvider;
use Silex\Application;

class LoggerProviderTest extends TestCase
{
    static private $silexApplication;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::registerServices();
    }

    private static function registerServices()
    {
        require_once __DIR__ . '/../../../vendor/autoload.php';
        $silexApplication = new Application(['monolog' => []]);
        $silexApplication->register(new LoggerProvider());

        self::$silexApplication = $silexApplication;
    }

    public function testConfigurationValidatedExists()
    {
        $this->assertArrayHasKey('log-processor-cfg-validated', self::$silexApplication);
    }

    public function testContextProcessorExists()
    {
        $this->assertArrayHasKey('context-log-processor', self::$silexApplication);
        $this->assertInstanceOf(SensitiveArrayProcessor::class, self::$silexApplication['context-log-processor']);
    }

    public function testMessageProcessorExists()
    {
        $this->assertArrayHasKey('message-log-processor', self::$silexApplication);
        $this->assertInstanceOf(SensitiveStringProcessor::class, self::$silexApplication['message-log-processor']);
    }

    public function testRecordProcessorExists()
    {
        $this->assertArrayHasKey('log-processor', self::$silexApplication);
        $this->assertInstanceOf(RecordLogProcessor::class, self::$silexApplication['log-processor']);
    }

    public function testProcessorFunctionExists()
    {
        $this->assertArrayHasKey('log-processor-function', self::$silexApplication);
        $this->assertInternalType('callable', self::$silexApplication['log-processor-function']);
    }

    public function testWriteMessageWithMonolog()
    {
        Logger::getInstance()->debug('Teste de senha');
    }
}

<?php declare(strict_types=1);

namespace ApiGen\Tests\Console\Command;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use ApiGen\Utils\FileSystem;

final class GenerateCommandPrepareOptionsTest extends AbstractContainerAwareTestCase
{
    /**
     * @var GenerateCommand
     */
    private $generateCommand;

    protected function setUp(): void
    {
        $this->generateCommand = $this->container->getByType(GenerateCommand::class);
        $this->fileSystem = new FileSystem;
    }

    /**
     * @expectedException \ApiGen\Configuration\Exceptions\ConfigurationException
     */
    public function testPrepareOptionsDestinationNotSet(): void
    {
        MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => 'config.neon'
        ]]);
    }

    public function testPrepareOptionsConfigPriority(): void
    {
        $configAndDestinationOptions = [
            'config' => __DIR__ . '/apigen.neon',
            'destination' => TEMP_DIR . '/api',
            'source' => [__DIR__]
        ];

        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [
            $configAndDestinationOptions
        ]);

        $this->assertSame(realpath(__DIR__ . '/../../../src'), $options['source'][0]);
    }

    public function testPrepareOptionsMergeIsCorrect(): void
    {
        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'source' => [__DIR__],
            'config' => __DIR__ . '/apigen.neon',
            'destination' => TEMP_DIR . '/api',
        ]]);

        $this->assertSame(['public', 'protected', 'private'], $options['accessLevels']);
        $this->assertSame('http://apigen.org', $options['baseUrl']);
    }
}

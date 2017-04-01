<?php declare(strict_types=1);

namespace ApiGen\Tests\Console\Command;

use ApiGen\Configuration\ConfigurationOptions;
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
            ConfigurationOptions::CONFIG => 'config.neon'
        ]]);
    }

    public function testPrepareOptionsConfigPriority(): void
    {
        $configAndDestinationOptions = [
            ConfigurationOptions::CONFIG => __DIR__ . '/apigen.neon',
            ConfigurationOptions::DESTINATION => TEMP_DIR . '/api',
            ConfigurationOptions::SOURCE => [__DIR__]
        ];

        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [
            $configAndDestinationOptions
        ]);

        $this->assertSame(realpath(__DIR__ . '/../../../src'), $options['source'][0]);
    }

    public function testPrepareOptionsMergeIsCorrect(): void
    {
        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            ConfigurationOptions::SOURCE => [__DIR__],
            ConfigurationOptions::CONFIG => __DIR__ . '/apigen.neon',
            ConfigurationOptions::DESTINATION => TEMP_DIR . '/api',
        ]]);

        $this->assertSame(1792, $options[ConfigurationOptions::VISIBILITY_LEVELS]);
        $this->assertSame('http://apigen.org', $options[ConfigurationOptions::BASE_URL]);
    }
}

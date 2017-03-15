<?php declare(strict_types=1);

namespace ApiGen\Tests\Command;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\Tests\ContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;

final class GenerateCommandPrepareOptionsTest extends ContainerAwareTestCase
{
    /**
     * @var GenerateCommand
     */
    private $generateCommand;


    protected function setUp(): void
    {
        $this->generateCommand = $this->container->getByType(GenerateCommand::class);
    }


    /**
     * @expectedException \ApiGen\Configuration\Exceptions\ConfigurationException
     */
    public function testPrepareOptionsDestinationNotSet(): void
    {
        MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => '...'
        ]]);
    }


    /**
     * @expectedException \ApiGen\Configuration\Exceptions\ConfigurationException
     */
    public function testPrepareOptionsSourceNotSet(): void
    {
        MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => '...',
            'destination' => TEMP_DIR . '/api'
        ]]);
    }


    public function testPrepareOptions(): void
    {
        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => '...',
            'destination' => TEMP_DIR . '/api',
            'source' => __DIR__
        ]]);

        $this->assertSame(TEMP_DIR . '/api', $options['destination']);
    }


    public function testPrepareOptionsConfigPriority(): void
    {
        $configAndDestinationOptions = [
            'config' => __DIR__ . '/apigen.neon',
            'destination' => TEMP_DIR . '/api',
            'source' => __DIR__
        ];

        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [
            $configAndDestinationOptions
        ]);

        $this->assertSame(realpath(__DIR__ . '/../../../src'), $options['source'][0]);
    }


    public function testPrepareOptionsMergeIsCorrect(): void
    {
        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'source' => __DIR__,
            'config' => __DIR__ . '/apigen.neon',
            'destination' => TEMP_DIR . '/api',
        ]]);

        $this->assertSame(['public', 'protected', 'private'], $options['accessLevels']);
        $this->assertSame('http://apigen.org', $options['baseUrl']);
    }


    public function testPrepareOptionsMergeIsCorrectFromYamlConfig(): void
    {
        $optionsYaml = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'source' => __DIR__,
            'config' => __DIR__ . '/apigen.yml',
            'destination' => TEMP_DIR . '/api',
        ]]);

        $optionsNeon = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'source' => __DIR__,
            'config' => __DIR__ . '/apigen.neon',
            'destination' => TEMP_DIR . '/api',
        ]]);

        $this->assertSame($optionsNeon, $optionsYaml);
    }


    public function testLoadOptionsFromConfig(): void
    {
        $options['config'] = '...';
        $options['destination'] = __DIR__;
        file_put_contents(getcwd() . '/apigen.neon.dist', 'debug: true');

        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'loadOptionsFromConfig', [$options]);
        $this->assertSame([
            'config' => '...',
            'destination' => __DIR__,
            'debug' => true
        ], $options);

        unlink(getcwd() . '/apigen.neon.dist');
    }
}

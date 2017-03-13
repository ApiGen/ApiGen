<?php

namespace ApiGen\Tests\Command;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\Tests\ContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;

class GenerateCommandPrepareOptionsTest extends ContainerAwareTestCase
{

    /**
     * @var GenerateCommand
     */
    private $generateCommand;


    protected function setUp()
    {
        $this->generateCommand = $this->container->getByType(GenerateCommand::class);
    }


    /**
     * @expectedException \ApiGen\Configuration\Exceptions\ConfigurationException
     */
    public function testPrepareOptionsDestinationNotSet()
    {
        MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => '...'
        ]]);
    }


    /**
     * @expectedException \ApiGen\Configuration\Exceptions\ConfigurationException
     */
    public function testPrepareOptionsSourceNotSet()
    {
        MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => '...',
            'destination' => TEMP_DIR . '/api'
        ]]);
    }


    public function testPrepareOptions()
    {
        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => '...',
            'destination' => TEMP_DIR . '/api',
            'source' => __DIR__
        ]]);

        $this->assertSame(TEMP_DIR . '/api', $options['destination']);
    }


    public function testPrepareOptionsConfigPriority()
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


    public function testPrepareOptionsMergeIsCorrect()
    {
        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => __DIR__ . '/apigen.neon',
            'destination' => TEMP_DIR . '/api',
        ]]);

        $this->assertSame(['public', 'protected', 'private'], $options['accessLevels']);
        $this->assertSame('http://apigen.org', $options['baseUrl']);
        $this->assertSame('packages', $options['groups']);
    }


    public function testPrepareOptionsMergeIsCorrectFromYamlConfig()
    {
        $optionsYaml = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => __DIR__ . '/apigen.yml',
            'destination' => TEMP_DIR . '/api',
        ]]);

        $optionsNeon = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => __DIR__ . '/apigen.neon',
            'destination' => TEMP_DIR . '/api',
        ]]);

        $this->assertSame($optionsNeon, $optionsYaml);
    }


    public function testLoadOptionsFromConfig()
    {
        $options['config'] = '...';
        file_put_contents(getcwd() . '/apigen.neon.dist', 'debug: true');

        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'loadOptionsFromConfig', [$options]);
        $this->assertSame([
            'config' => '...',
            'debug' => true
        ], $options);

        unlink(getcwd() . '/apigen.neon.dist');
    }
}

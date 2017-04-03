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

<<<<<<< HEAD
    protected function setUp(): void
||||||| merged common ancestors

    protected function setUp()
=======
    /**
     * @var FileSystem
     */
    private $fileSystem;


    protected function setUp()
>>>>>>> 4.2
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
<<<<<<< HEAD
            'config' => 'config.neon'
||||||| merged common ancestors
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
=======
            'config' => '...',
            'destination' => TEMP_DIR . DIRECTORY_SEPARATOR . 'api'
        ]]);
    }


    public function testPrepareOptions()
    {
        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => '...',
            'destination' => TEMP_DIR . DIRECTORY_SEPARATOR . 'api',
            'source' => __DIR__
>>>>>>> 4.2
        ]]);
<<<<<<< HEAD
||||||| merged common ancestors

        $this->assertSame(TEMP_DIR . '/api', $options['destination']);
=======

        $this->assertSame($this->fileSystem->getAbsolutePath(TEMP_DIR . '/api'), $options['destination']);
>>>>>>> 4.2
    }

    public function testPrepareOptionsConfigPriority(): void
    {
        $configAndDestinationOptions = [
<<<<<<< HEAD
            'config' => __DIR__ . '/apigen.neon',
            'destination' => TEMP_DIR . '/api',
            'source' => [__DIR__]
||||||| merged common ancestors
            'config' => __DIR__ . '/apigen.neon',
            'destination' => TEMP_DIR . '/api',
            'source' => __DIR__
=======
            'config' => __DIR__ . DIRECTORY_SEPARATOR . 'apigen.neon',
            'destination' => TEMP_DIR . DIRECTORY_SEPARATOR . 'api',
            'source' => __DIR__
>>>>>>> 4.2
        ];

        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [
            $configAndDestinationOptions
        ]);
<<<<<<< HEAD

        $this->assertSame(realpath(__DIR__ . '/../../../src'), $options['source'][0]);
||||||| merged common ancestors
        $this->assertSame(realpath(__DIR__ . '/../../../src'), $options['source'][0]);
=======
        $this->assertSame($this->fileSystem->getAbsolutePath(__DIR__ . '/../../../src'), $options['source'][0]);
>>>>>>> 4.2
    }

    public function testPrepareOptionsMergeIsCorrect(): void
    {
        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
<<<<<<< HEAD
            'source' => [__DIR__],
            'config' => __DIR__ . '/apigen.neon',
            'destination' => TEMP_DIR . '/api',
||||||| merged common ancestors
            'config' => __DIR__ . '/apigen.neon',
            'destination' => TEMP_DIR . '/api',
            'download' => false
=======
            'config' => __DIR__ . DIRECTORY_SEPARATOR . 'apigen.neon',
            'destination' => TEMP_DIR . DIRECTORY_SEPARATOR . 'api',
            'download' => false
>>>>>>> 4.2
        ]]);

        $this->assertSame(['public', 'protected', 'private'], $options['accessLevels']);
        $this->assertSame('http://apigen.org', $options['baseUrl']);
<<<<<<< HEAD
||||||| merged common ancestors
        $this->assertTrue($options['download']);
        $this->assertSame('packages', $options['groups']);
        $this->assertFalse($options['todo']);
    }


    public function testPrepareOptionsMergeIsCorrectFromYamlConfig()
    {
        $optionsYaml = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => __DIR__ . '/apigen.yml',
            'destination' => TEMP_DIR . '/api',
            'download' => false
        ]]);

        $optionsNeon = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => __DIR__ . '/apigen.neon',
            'destination' => TEMP_DIR . '/api',
            'download' => false
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
=======
        $this->assertTrue($options['download']);
        $this->assertSame('packages', $options['groups']);
        $this->assertFalse($options['todo']);
    }


    public function testPrepareOptionsMergeIsCorrectFromYamlConfig()
    {
        $optionsYaml = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => __DIR__ . DIRECTORY_SEPARATOR . 'apigen.yml',
            'destination' => TEMP_DIR . DIRECTORY_SEPARATOR . 'api',
            'download' => false
        ]]);

        $optionsNeon = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
            'config' => __DIR__ . DIRECTORY_SEPARATOR . 'apigen.neon',
            'destination' => TEMP_DIR . DIRECTORY_SEPARATOR . 'api',
            'download' => false
        ]]);

        $this->assertSame($optionsNeon, $optionsYaml);
    }


    public function testLoadOptionsFromConfig()
    {
        $options['config'] = '...';
        file_put_contents(getcwd() . DIRECTORY_SEPARATOR . 'apigen.neon.dist', 'debug: true');

        $options = MethodInvoker::callMethodOnObject($this->generateCommand, 'loadOptionsFromConfig', [$options]);
        $this->assertSame([
            'config' => '...',
            'debug' => true
        ], $options);

        unlink(getcwd() . DIRECTORY_SEPARATOR . 'apigen.neon.dist');
>>>>>>> 4.2
    }
}

<?php

namespace ApiGen\Tests\Command;

use ApiGen\Command\GenerateCommand;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Configuration\Exceptions\ConfigurationException;
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


	public function testPrepareOptionsDestinationNotSet()
	{
		$this->setExpectedException(ConfigurationException::class, 'Destination is not set');
		MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
			'config' => '...'
		]]);
	}


	public function testPrepareOptionsSourceNotSet()
	{

		$this->setExpectedException(ConfigurationException::class, 'Source is not set');
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
		$this->assertSame(realpath(__DIR__ . '/../../src'), $options['source'][0]);
	}


	public function testPrepareOptionsMergeIsCorrect()
	{
		$options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
			'config' => __DIR__ . '/apigen.neon',
			'destination' => TEMP_DIR . '/api',
			'download' => FALSE
		]]);

		$this->assertSame(['public', 'protected', 'private'], $options[CO::ACCESS_LEVELS]);
		$this->assertSame('http://apigen.org', $options[CO::BASE_URL]);
		$this->assertTrue($options[CO::DEPRECATED]);
		$this->assertTrue($options[CO::DOWNLOAD]);
		$this->assertSame('packages', $options[CO::GROUPS]);
		$this->assertTrue($options[CO::TODO]);
		$this->assertTrue($options[CO::TREE]);
	}


	public function testPrepareOptionsMergeIsCorrectFromYamlConfig()
	{
		$optionsYaml = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
			'config' => __DIR__ . '/apigen.yml',
			'destination' => TEMP_DIR . '/api',
			'download' => FALSE
		]]);

		$optionsNeon = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
			'config' => __DIR__ . '/apigen.neon',
			'destination' => TEMP_DIR . '/api',
			'download' => FALSE
		]]);

		$this->assertSame($optionsNeon, $optionsYaml);
	}

}

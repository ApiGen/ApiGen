<?php

namespace ApiGen\Tests\Command;

use ApiGen;
use ApiGen\Command\GenerateCommand;
use ApiGen\Configuration\ConfigurationOptions as CO;
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
		$this->generateCommand = $this->container->getByType('ApiGen\Command\GenerateCommand');
	}


	/**
	 * @expectedException ApiGen\Configuration\Exceptions\ConfigurationException
	 * @expectedExceptionMessageRegExp /Destination is not set/
	 */
	public function testPrepareOptionsDestinationNotSet()
	{
		MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
			'config' => '...'
		]]);
	}


	/**
	 * @expectedException ApiGen\Configuration\Exceptions\ConfigurationException
	 * @expectedExceptionMessageRegExp /Source is not set/
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

}

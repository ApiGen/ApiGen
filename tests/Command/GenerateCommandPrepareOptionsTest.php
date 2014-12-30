<?php

namespace ApiGen\Tests\Command;

use ApiGen;
use ApiGen\Command\GenerateCommand;
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
	 * @expectedException \ApiGen\Configuration\Exceptions\ConfigurationException
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


	public function testPrepareOptionsCliPriority()
	{
		$configAndDestinationOptions = [
			'config' => __DIR__ . '/apigen.neon',
			'destination' => TEMP_DIR . '/api'
		];

		$options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [
			$configAndDestinationOptions + ['source' => []]
		]);
		$this->assertContains('src', $options['source'][0]);

		$options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [
			$configAndDestinationOptions + ['source' => __DIR__]
		]);
		$this->assertSame(__DIR__, $options['source'][0]);
	}


	public function testPrepareOptionsWithConfigBool()
	{
		$options = MethodInvoker::callMethodOnObject($this->generateCommand, 'prepareOptions', [[
			'config' => __DIR__ . '/apigen.neon',
			'destination' => TEMP_DIR . '/api',
			'tree' => FALSE
		]]);

		$this->assertFalse($options['tree']);
	}

}

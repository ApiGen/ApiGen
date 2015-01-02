<?php

namespace ApiGen\Tests\Console\Input;

use ApiGen\Console\Input\LiberalFormatArgvInput;
use ApiGen\Tests\MethodInvoker;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;


class LiberalFormatArgvInputTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var LiberalFormatArgvInput
	 */
	private $formatLiberalArgvInput;


	protected function setUp()
	{
		$inputDefinition = new InputDefinition([
			new InputOption('source', NULL, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED),
			new InputOption('destination', NULL, InputOption::VALUE_REQUIRED)
		]);
		$this->formatLiberalArgvInput = new LiberalFormatArgvInput([], $inputDefinition);
	}


	public function testGetOption()
	{
		$this->formatLiberalArgvInput->setOption('source', ['one,two']);
		$this->assertSame(['one', 'two'], $this->formatLiberalArgvInput->getOption('source'));
	}


	public function testGetOptions()
	{
		$this->formatLiberalArgvInput->setOption('source', ['one,two']);
		$this->assertSame(['one', 'two'], $this->formatLiberalArgvInput->getOptions()['source']);
	}


	/**
	 * @dataProvider getSplitByComma()
	 */
	public function testSplitByComma($input, $expected)
	{
		$this->assertSame(
			$expected,
			MethodInvoker::callMethodOnObject($this->formatLiberalArgvInput, 'splitByComma', [$input])
		);
	}


	/**
	 * @return array[]
	 */
	public function getSplitByComma()
	{
		return [
			[['one,two'], ['one', 'two']],
			['one,two', ['one', 'two']]
		];
	}


	/**
	 * @dataProvider getRemoveEqualsData()
	 */
	public function testRemoveEquals($input, $expected)
	{
		$this->assertSame(
			$expected,
			MethodInvoker::callMethodOnObject($this->formatLiberalArgvInput, 'removeEqualsSign', [$input])
		);
	}


	/**
	 * @return array[]
	 */
	public function getRemoveEqualsData()
	{
		return [
			['=something', 'something'],
			[['=something'], ['something']]
		];
	}

}

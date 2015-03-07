<?php

namespace ApiGen\Tests;

use ApiGen\MemoryLimit;
use PHPUnit_Framework_TestCase;


class MemoryLimitTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var MemoryLimit
	 */
	private $memoryLimit;


	protected function setUp()
	{
		$this->memoryLimit = new MemoryLimit;
	}


	/**
	 * @dataProvider getMemoryToBytesConversionData()
	 * @param string $memory
	 * @param int $inBytes
	 */
	public function testMemoryToBytesConversion($memory, $inBytes)
	{
		$this->assertSame(
			$inBytes,
			MethodInvoker::callMethodOnObject($this->memoryLimit, 'getMemoryInBytes', [$memory])
		);
	}


	/**
	 * @return array
	 */
	public function getMemoryToBytesConversionData()
	{
		return [
			['1000k', 1024 * 1000],
			['1000m', 1024 * 1024 * 1000],
			['1000g', 1024 * 1024 * 1024 * 1000]
		];
	}

}

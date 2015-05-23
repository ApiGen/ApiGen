<?php

namespace ApiGen\Utils\Tests\Finder;

use ApiGen\Utils\Finder\NetteFinder;
use PHPUnit_Framework_TestCase;


class NetteFinderTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var NetteFinder
	 */
	private $netteFinder;


	protected function setUp()
	{
		$this->netteFinder = new NetteFinder;
	}


	public function testSource()
	{
		$this->assertCount(1, $this->netteFinder->find(__DIR__ . '/NetteFinderSource'));
		$this->assertCount(1, $this->netteFinder->find([__DIR__ . '/NetteFinderSource']));
	}


	public function testExclude()
	{
		$this->assertCount(0, $this->netteFinder->find(__DIR__ . '/NetteFinderSource', ['SomeClass.php']));
	}


	public function testExtensions()
	{
		$this->assertCount(0, $this->netteFinder->find(__DIR__ . '/NetteFinderSource', [], ['php5']));
	}

}

<?php

namespace ApiGen\Utils\Tests\Finder;

use ApiGen\Utils\Finder\FinderInterface;
use ApiGen\Utils\Finder\NetteFinder;
use PHPUnit_Framework_TestCase;


class NetteFinderTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var FinderInterface
	 */
	private $finder;


	protected function setUp()
	{
		$this->finder = new NetteFinder;
	}


	public function testSource()
	{
		$this->assertCount(1, $this->finder->find(__DIR__ . '/NetteFinderSource'));
		$this->assertCount(1, $this->finder->find([__DIR__ . '/NetteFinderSource']));

		$files = $this->finder->find(__DIR__ . '/Source');
		$this->assertCount(4, $files);

		$files = $this->finder->find(__DIR__ . '/Source', ['*Another*']);
		$this->assertCount(3, $files);

		$files = $this->finder->find(__DIR__ . '/Source', [], ['php5']);
		$this->assertCount(1, $files);
	}


	public function testFindSingleFile()
	{
		$files = $this->finder->find(__DIR__ . '/Source/SomeClass.php');
		$this->assertCount(1, $files);
	}


	public function testExclude()
	{
		$this->assertCount(0, $this->finder->find(__DIR__ . '/NetteFinderSource', ['SomeClass.php']));
	}


	public function testExtensions()
	{
		$this->assertCount(0, $this->finder->find(__DIR__ . '/NetteFinderSource', [], ['php5']));
	}


	public function testNoFound()
	{
		$this->assertCount(0, $this->finder->find(__DIR__ . '/Source', [], ['php6']));
	}

}

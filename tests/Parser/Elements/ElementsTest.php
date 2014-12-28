<?php

namespace ApiGen\Tests\Parser\Elements;

use ApiGen\Parser\Elements\Elements;
use PHPUnit_Framework_TestCase;


class ElementsTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var Elements
	 */
	private $elements;


	protected function setUp()
	{
		$this->elements = new Elements;
	}


	public function testGetClassTypeList()
	{
		$this->assertSame(
			['classes', 'exceptions', 'interfaces', 'traits'],
			$this->elements->getClassTypeList()
		);
	}


	public function testGetAll()
	{
		$this->assertSame(
			['classes', 'constants', 'exceptions', 'functions', 'interfaces', 'traits'],
			$this->elements->getAll()
		);
	}


	public function testGetEmptyList()
	{
		$this->assertSame(
			['classes' => [], 'constants' => [], 'exceptions' => [], 'functions' => [], 'interfaces' => [], 'traits' => []],
			$this->elements->getEmptyList()
		);
	}

}

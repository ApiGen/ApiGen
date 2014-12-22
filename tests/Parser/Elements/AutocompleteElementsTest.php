<?php

namespace ApiGen\Tests\Parser\Elements;

use ApiGen\Parser\Elements\AutocompleteElements;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use PHPUnit_Framework_TestCase;


class AutocompleteElementsTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var AutocompleteElements
	 */
	private $autocompleteElements;


	protected function setUp()
	{
		$methodReflectionMock = Mockery::mock('ApiGen\Reflection\ReflectionMethod');
		$methodReflectionMock->shouldReceive('getPrettyName')->andReturn('MethodPrettyName');

		$classReflectionMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$classReflectionMock->shouldReceive('getPrettyName')->andReturn('ClassPrettyName');
		$classReflectionMock->shouldReceive('getOwnConstants')->andReturn([]);
		$classReflectionMock->shouldReceive('getOwnMethods')->andReturn([$methodReflectionMock]);
		$classReflectionMock->shouldReceive('getOwnMagicMethods')->andReturn([]);

		$constantReflectionMock = Mockery::mock('ApiGen\Reflection\ReflectionConstant');
		$constantReflectionMock->shouldReceive('getPrettyName')->andReturn('ConstantPrettyName');

		$functionReflectionMock = Mockery::mock('ApiGen\Reflection\ReflectionFunction');
		$functionReflectionMock->shouldReceive('getPrettyName')->andReturn('FunctionPrettyName');

		$elementsStorageMock = Mockery::mock('ApiGen\Parser\Elements\ElementStorage');
		$elementsStorageMock->shouldReceive('getElements')->andReturn([
			'classes' => [$classReflectionMock],
			'constants' => [$constantReflectionMock],
			'functions' => [$functionReflectionMock]
		]);

		$this->autocompleteElements = new AutocompleteElements($elementsStorageMock);
	}


	public function testGetElementsClasses()
	{
		$elements = $this->autocompleteElements->getElements();
		$this->assertSame([
			['c', 'ClassPrettyName'],
			['co', 'ConstantPrettyName'],
			['f', 'FunctionPrettyName']
		], $elements);
	}

}

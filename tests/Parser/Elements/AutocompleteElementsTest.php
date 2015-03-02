<?php

namespace ApiGen\Tests\Parser\Elements;

use ApiGen\Parser\Elements\AutocompleteElements;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionFunction;
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
		$classReflectionMock = Mockery::mock(ReflectionClass::class);
		$classReflectionMock->shouldReceive('getPrettyName')->andReturn('ClassPrettyName');
		$classReflectionMock->shouldReceive('getOwnConstants')->andReturn([]);

		$constantReflectionMock = Mockery::mock(ReflectionConstant::class);
		$constantReflectionMock->shouldReceive('getPrettyName')->andReturn('ConstantPrettyName');

		$functionReflectionMock = Mockery::mock(ReflectionFunction::class);
		$functionReflectionMock->shouldReceive('getPrettyName')->andReturn('FunctionPrettyName');

		$elementsStorageMock = Mockery::mock(ElementStorage::class);
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

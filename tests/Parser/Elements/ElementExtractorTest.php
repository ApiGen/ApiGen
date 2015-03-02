<?php

namespace ApiGen\Tests\Parser\Elements;

use ApiGen\Parser\Elements\ElementExtractor;
use ApiGen\Parser\Elements\ElementFilter;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Parser\Elements\ElementSorter;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionElement;
use Mockery;
use PHPUnit_Framework_TestCase;


class ElementExtractorTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ElementExtractor
	 */
	private $elementExtractor;


	protected function setUp()
	{
		$elementFilterMock = Mockery::mock(ElementFilter::class);
		$elementFilterMock->shouldReceive('filterForMain')->andReturnUsing(function ($elements) {
			return $elements;
		});
		$elementFilterMock->shouldReceive('filterByAnnotation')->andReturnUsing(function ($elements) {
			return $elements;
		});

		$elementStorageMock = Mockery::mock(ElementStorage::class);
		$elementStorageMock->shouldReceive('getElements')->andReturn([
			'classes' => $this->getReflectionClassMocks(),
			'constants' => []
		]);

		$elementSorterMock = Mockery::mock(ElementSorter::class);
		$elementSorterMock->shouldReceive('sortElementsByFqn')->andReturnUsing(function ($elements) {
			return $elements;
		});

		$this->elementExtractor = new ElementExtractor(
			new Elements, $elementFilterMock, $elementStorageMock, $elementSorterMock
		);
	}


	public function testExtractElementsByAnnotation()
	{
		$deprecatedElements = $this->elementExtractor->extractElementsByAnnotation('deprecated');

		$this->assertInternalType('array', $deprecatedElements);
		$this->assertCount(8, $deprecatedElements);

		$this->assertArrayHasKey('classes', $deprecatedElements);
		$this->assertArrayHasKey('traits', $deprecatedElements);
		$this->assertArrayHasKey('interfaces', $deprecatedElements);
		$this->assertArrayHasKey('exceptions', $deprecatedElements);
		$this->assertArrayHasKey('constants', $deprecatedElements);
		$this->assertArrayHasKey('functions', $deprecatedElements);
		$this->assertArrayHasKey('methods', $deprecatedElements);
		$this->assertArrayHasKey('properties', $deprecatedElements);

		$this->assertCount(3, $deprecatedElements['classes']);
	}


	public function testExtractElementsByAnnotationWithCallback()
	{
		$deprecatedElements = $this->elementExtractor->extractElementsByAnnotation('deprecated', function ($element) {
			/** @var ReflectionElement $element */
			return $element->isDeprecated();
		});
		$this->assertCount(2, $deprecatedElements['methods']);
	}


	/**
	 * @return ReflectionClass[]
	 */
	private function getReflectionClassMocks()
	{
		$reflectionClassMock = Mockery::mock(ReflectionClass::class);
		$reflectionClassMock->shouldReceive('getOwnConstants')->andReturn([]);
		$reflectionClassMock->shouldReceive('getOwnProperties')->andReturn([]);

		$reflectionClassMock2 = clone $reflectionClassMock;
		$reflectionClassMock2->shouldReceive('isMain')->andReturn(TRUE);
		$reflectionClassMock2->shouldReceive('isDeprecated')->andReturn(FALSE);
		$reflectionClassMock2->shouldReceive('getOwnMethods')->andReturn([1, 2]);

		$reflectionClassMock3 = clone $reflectionClassMock;
		$reflectionClassMock3->shouldReceive('isMain')->andReturn(TRUE);
		$reflectionClassMock3->shouldReceive('isDeprecated')->andReturn(TRUE);
		$reflectionClassMock3->shouldReceive('getOwnMethods')->andReturn([3, 4]);

		$reflectionClassMock->shouldReceive('getOwnMethods')->andReturn([]);
		$reflectionClassMock->shouldReceive('isMain')->andReturn(FALSE);
		$reflectionClassMock->shouldReceive('isDeprecated')->andReturn(TRUE);

		return [$reflectionClassMock, $reflectionClassMock2, $reflectionClassMock3];
	}

}

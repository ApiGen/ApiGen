<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Templating\Filters\AnnotationFilters;
use Mockery;
use PHPUnit_Framework_TestCase;


class AnnotationFiltersTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var AnnotationFilters
	 */
	private $annotationFilters;


	protected function setUp()
	{
		$configurationMock = Mockery::mock(Configuration::class);
		$configurationMock->shouldReceive('getOption')->with(CO::INTERNAL)->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with(CO::TODO)->andReturn(FALSE);
		$elementResolverMock = Mockery::mock(ElementResolver::class);
		$this->annotationFilters = new AnnotationFilters($configurationMock, $elementResolverMock);
	}


	public function testAnnotationBeautify()
	{
		$this->assertSame('Used by', $this->annotationFilters->annotationBeautify('usedby'));
		$this->assertSame('Method', $this->annotationFilters->annotationBeautify('method'));
	}


	public function testAnnotationFilter()
	{
		$annotations = ['method' => TRUE, 'remain' => TRUE, 'todo' => TRUE, 'internal' => TRUE];
		$this->assertSame(
			['remain' => TRUE],
			$this->annotationFilters->annotationFilter($annotations)
		);
	}


	public function testAnnotationFilterWithCustom()
	{
		$annotations = ['remain' => TRUE, 'otherToRemain' => TRUE];
		$this->assertSame(
			['otherToRemain' => TRUE],
			$this->annotationFilters->annotationFilter($annotations, ['remain'])
		);
	}


	public function testAnnotationSort()
	{
		$annotations = ['method' => TRUE, 'see' => TRUE, 'package' => TRUE, 'property' => TRUE, 'deprecated' => TRUE];
		$this->assertSame(
			['deprecated' => TRUE, 'see' => TRUE, 'method' => TRUE, 'package' => TRUE, 'property' => TRUE],
			$this->annotationFilters->annotationSort($annotations)
		);
	}

}

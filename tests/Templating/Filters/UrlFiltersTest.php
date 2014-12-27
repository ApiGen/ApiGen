<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Templating\Filters\UrlFilters;
use Mockery;
use PHPUnit_Framework_TestCase;


class UrlFiltersTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var UrlFilters
	 */
	private $urlFilters;


	protected function setUp()
	{
		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
		$configurationMock->shouldReceive('getOption')->with(CO::TEMPLATE)->andReturn([
			'templates' => [
				'class' => ['filename' => 'class-%s'],
				'constant' => ['filename' => 'constant-%s'],
				'function' => ['filename' => 'function-%s']
			]
		]);
		$markupMock = Mockery::mock('ApiGen\Generator\Markups\Markup');
		$sourceCodeHighlighterMock = Mockery::mock('ApiGen\Generator\SourceCodeHighlighter\SourceCodeHighlighter');
		$elementResolverMock = Mockery::mock('ApiGen\Generator\Resolvers\ElementResolver');
		$linkBuilderMock = Mockery::mock('ApiGen\Templating\Filters\Helpers\LinkBuilder');
		$this->urlFilters = new UrlFilters(
			$configurationMock, $sourceCodeHighlighterMock, $markupMock, $elementResolverMock, $linkBuilderMock
		);
	}


	public function testElementUrl()
	{
		$this->assertSame(
			'class-SomeNamespace.SomeClass',
			$this->urlFilters->elementUrl($this->getReflectionClassMock())
		);

		$reflectionMethodMock = $this->getReflectionMethodMock();
		$reflectionMethodMock->shouldReceive('isMagic')->andReturn(FALSE);
		$reflectionMethodMock->shouldReceive('getOriginalName')->andReturnNull();
		$this->assertSame('class-SomeClass#_getSomeMethod', $this->urlFilters->elementUrl($reflectionMethodMock));

		$reflectionPropertyMock = $this->getReflectionPropertyMock();
		$reflectionPropertyMock->shouldReceive('isMagic')->andReturn(FALSE);
		$this->assertSame('class-SomeClass#$someProperty', $this->urlFilters->elementUrl($reflectionPropertyMock));

		$reflectionConstantMock = $this->getReflectionConstantMock();
		$reflectionConstantMock->shouldReceive('getDeclaringClassName')->once()->andReturn('SomeClass');
		$this->assertSame('class-SomeClass#someConstant', $this->urlFilters->elementUrl($reflectionConstantMock));

		$this->assertSame('function-someFunction', $this->urlFilters->elementUrl($this->getReflectionFunctionMock()));

		$reflectionElementMock = Mockery::mock('ApiGen\Reflection\ReflectionElement');
		$this->assertNull($this->urlFilters->elementUrl($reflectionElementMock));
	}


	public function testClassUrl()
	{
		$this->assertSame(
			'class-SomeNamespace.SomeClass',
			$this->urlFilters->classUrl($this->getReflectionClassMock())
		);
		$this->assertSame('class-SomeStringClass', $this->urlFilters->classUrl('SomeStringClass'));
	}


	public function testMethodUrl()
	{
		$reflectionMethodMock = $this->getReflectionMethodMock();
		$reflectionMethodMock->shouldReceive('isMagic')->andReturn(FALSE);

		$reflectionMethodMock->shouldReceive('getOriginalName')->once()->andReturn('getSomeMethodOriginal');
		$this->assertSame(
			'class-SomeClass#_getSomeMethodOriginal',
			$this->urlFilters->methodUrl($reflectionMethodMock)
		);

		$reflectionMethodMock->shouldReceive('getOriginalName')->twice()->andReturnNull();
		$this->assertSame('class-SomeClass#_getSomeMethod', $this->urlFilters->methodUrl($reflectionMethodMock));
	}


	public function testMethodUrlWithSeparateClass()
	{
		$reflectionMethodMock = $this->getReflectionMethodMock();
		$reflectionMethodMock->shouldReceive('getOriginalName')->andReturnNull();
		$reflectionMethodMock->shouldReceive('isMagic')->andReturn(FALSE);

		$this->assertSame(
			'class-SomeNamespace.SomeClass#_getSomeMethod',
			$this->urlFilters->methodUrl($reflectionMethodMock, $this->getReflectionClassMock())
		);
	}


	public function testMethodUrlWithMagicMethod()
	{
		$reflectionMethodMock = $this->getReflectionMethodMock();
		$reflectionMethodMock->shouldReceive('getOriginalName')->andReturnNull();
		$reflectionMethodMock->shouldReceive('isMagic')->andReturn(TRUE);

		$this->assertSame('class-SomeClass#m_getSomeMethod', $this->urlFilters->methodUrl($reflectionMethodMock));
	}


	public function testPropertyUrl()
	{
		$reflectionPropertyMock = $this->getReflectionPropertyMock();
		$reflectionPropertyMock->shouldReceive('isMagic')->andReturn(FALSE);

		$this->assertSame('class-SomeClass#$someProperty', $this->urlFilters->propertyUrl($reflectionPropertyMock));
	}


	public function testPropertyUrlWithSeparateClass()
	{
		$reflectionPropertyMock = $this->getReflectionPropertyMock();
		$reflectionPropertyMock->shouldReceive('isMagic')->andReturn(FALSE);

		$this->assertSame(
			'class-SomeNamespace.SomeClass#$someProperty',
			$this->urlFilters->propertyUrl($reflectionPropertyMock, $this->getReflectionClassMock())
		);
	}


	public function testPropertyUrlWithMagicMethod()
	{
		$reflectionPropertyMock = $this->getReflectionPropertyMock();
		$reflectionPropertyMock->shouldReceive('getOriginalName')->andReturnNull();
		$reflectionPropertyMock->shouldReceive('isMagic')->andReturn(TRUE);
		$this->assertSame('class-SomeClass#m$someProperty', $this->urlFilters->propertyUrl($reflectionPropertyMock));
	}


	public function testConstantUrl()
	{
		$reflectionConstantMock = $this->getReflectionConstantMock();

		$reflectionConstantMock->shouldReceive('getDeclaringClassName')->once()->andReturn('SomeClass');
		$this->assertSame('class-SomeClass#someConstant', $this->urlFilters->constantUrl($reflectionConstantMock));

		$reflectionConstantMock->shouldReceive('getDeclaringClassName')->twice()->andReturnNull();
		$this->assertSame('constant-someConstant', $this->urlFilters->constantUrl($reflectionConstantMock));
	}


	public function testFunctionUrl()
	{
		$reflectionFunctionMock = $this->getReflectionFunctionMock();
		$this->assertSame('function-someFunction', $this->urlFilters->functionUrl($reflectionFunctionMock));
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionMethodMock()
	{
		$reflectionMethodMock = Mockery::mock('ApiGen\Reflection\ReflectionMethod');
		$reflectionMethodMock->shouldReceive('getName')->andReturn('getSomeMethod');
		$reflectionMethodMock->shouldReceive('getDeclaringClassName')->andReturn('SomeClass');
		return $reflectionMethodMock;
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionClassMock()
	{
		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$reflectionClassMock->shouldReceive('getName')->andReturn('SomeNamespace\\SomeClass');
		return $reflectionClassMock;
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionPropertyMock()
	{
		$reflectionPropertyMock = Mockery::mock('ApiGen\Reflection\ReflectionProperty');
		$reflectionPropertyMock->shouldReceive('getName')->andReturn('someProperty');
		$reflectionPropertyMock->shouldReceive('getDeclaringClassName')->andReturn('SomeClass');
		return $reflectionPropertyMock;
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionConstantMock()
	{
		$reflectionConstantMock = Mockery::mock('ApiGen\Reflection\ReflectionConstant');
		$reflectionConstantMock->shouldReceive('getName')->andReturn('someConstant');
		return $reflectionConstantMock;
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionFunctionMock()
	{
		$reflectionFunctionMock = Mockery::mock('ApiGen\Reflection\ReflectionFunction');
		$reflectionFunctionMock->shouldReceive('getName')->andReturn('someFunction');
		$reflectionFunctionMock->shouldReceive('getDeclaringClassName')->andReturn('SomeClass');
		return $reflectionFunctionMock;
	}

}

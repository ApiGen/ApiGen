<?php

namespace ApiGen\Tests\Generator\Resolvers;

use ApiGen\Generator\Resolvers\ElementResolver;
use Mockery;
use PHPUnit_Framework_TestCase;


class ElementResolverTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ElementResolver
	 */
	private $elementResolver;


	protected function setUp()
	{
		$elementReflection = Mockery::mock('ApiGen\Reflection\ReflectionElement');
		$elementReflection->shouldReceive('isDocumented')->andReturn(TRUE);

		$notDocumentedElementReflection = Mockery::mock('ApiGen\Reflection\ReflectionElement');
		$notDocumentedElementReflection->shouldReceive('isDocumented')->andReturn(FALSE);

		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
		$parserResultMock->shouldReceive('getClasses')->andReturn([
			'SomeClass' => $elementReflection,
			'SomeNamespace\SomeClass' => $elementReflection,
			'SomeNotDocumentedClass' => $notDocumentedElementReflection
		]);
		$parserResultMock->shouldReceive('getConstants')->andReturn([
			'SomeConstant' => $elementReflection,
		]);
		$parserResultMock->shouldReceive('getFunctions')->andReturn([
			'SomeFunction' => $elementReflection,
		]);
		$this->elementResolver = new ElementResolver($parserResultMock);
	}


	public function testGetClass()
	{
		$element = $this->elementResolver->getClass('SomeClass');
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionElement', $element);
		$this->assertTrue($element->isDocumented());

		$element = $this->elementResolver->getClass('SomeClass', 'SomeNamespace');
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionElement', $element);
		$this->assertTrue($element->isDocumented());
	}


	public function testGetClassNotExisting()
	{
		$this->assertNull($this->elementResolver->getClass('NotExistingClass'));
	}


	public function testGetClassNotDocumented()
	{
		$this->assertNull($this->elementResolver->getClass('SomeNotDocumentedClass'));
	}


	public function testGetConstant()
	{
		$element = $this->elementResolver->getConstant('SomeConstant');
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionElement', $element);
		$this->assertTrue($element->isDocumented());
	}


	public function testGetConstantNotExisting()
	{
		$this->assertNull($this->elementResolver->getConstant('NotExistingConstant'));
	}


	public function testGetFunction()
	{
		$element = $this->elementResolver->getFunction('SomeFunction');
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionElement', $element);
		$this->assertTrue($element->isDocumented());
	}


	public function testGetConstantNotFunction()
	{
		$this->assertNull($this->elementResolver->getFunction('NotExistingFunction'));
	}

}

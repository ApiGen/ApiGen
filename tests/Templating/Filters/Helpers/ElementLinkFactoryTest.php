<?php

namespace ApiGen\Tests\Templating\Filters\Helpers;

use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionProperty;
use ApiGen\Templating\Filters\Helpers\ElementLinkFactory;
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use Mockery;
use PHPUnit_Framework_TestCase;
use UnexpectedValueException;


class ElementLinkFactoryTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ElementLinkFactory
	 */
	private $elementLinkFactory;


	protected function setUp()
	{
		$this->elementLinkFactory = new ElementLinkFactory($this->getElementUrlFactoryMock(), new LinkBuilder);
	}


	public function testCreateForElementClass()
	{
		$reflectionClass = Mockery::mock(ReflectionClass::class);
		$reflectionClass->shouldReceive('getName')->andReturn('SomeClass');
		$reflectionClass->shouldReceive('getDeclaringClassName')->andReturn('declaringClass');

		$this->assertSame(
			'<a href="class-link-SomeClass">SomeClass</a>',
			 $this->elementLinkFactory->createForElement($reflectionClass)
		);
	}


	public function testCreateForFunction()
	{
		$reflectionFunction = Mockery::mock(ReflectionFunction::class);
		$reflectionFunction->shouldReceive('getName')->andReturn('getSome');
		$reflectionFunction->shouldReceive('getDeclaringClassName')->andReturn('DeclaringClass');

		$this->assertSame(
			'<a href="function-link-getSome">getSome()</a>',
			$this->elementLinkFactory->createForElement($reflectionFunction)
		);
	}


	public function testCreateForConstant()
	{
		$reflectionConstant = Mockery::mock(ReflectionConstant::class);
		$reflectionConstant->shouldReceive('getName')->andReturn('SOME_CONSTANT');
		$reflectionConstant->shouldReceive('getDeclaringClassName')->andReturnNull();
		$reflectionConstant->shouldReceive('inNamespace')->andReturn(FALSE);

		$this->assertSame(
			'<a href="constant-link-SOME_CONSTANT"><b>SOME_CONSTANT</b></a>',
			$this->elementLinkFactory->createForElement($reflectionConstant)
		);
	}


	public function testCreateForConstantInClass()
	{
		$reflectionConstant = Mockery::mock(ReflectionConstant::class);
		$reflectionConstant->shouldReceive('getName')->andReturn('SOME_CONSTANT');
		$reflectionConstant->shouldReceive('getDeclaringClassName')->andReturn('DeclaringClass');

		$this->assertSame(
			'<a href="constant-link-SOME_CONSTANT">DeclaringClass::<b>SOME_CONSTANT</b></a>',
			$this->elementLinkFactory->createForElement($reflectionConstant)
		);
	}


	public function testCreateForElementConstantInNamespace()
	{
		$reflectionConstant = Mockery::mock(ReflectionConstant::class);
		$reflectionConstant->shouldReceive('getName')->andReturn('SOME_CONSTANT');
		$reflectionConstant->shouldReceive('getShortName')->andReturn('SHORT_SOME_CONSTANT');
		$reflectionConstant->shouldReceive('getDeclaringClassName')->andReturnNull();
		$reflectionConstant->shouldReceive('inNamespace')->andReturn(TRUE);
		$reflectionConstant->shouldReceive('getNamespaceName')->andReturn('Namespace');

		$this->assertSame(
			'<a href="constant-link-SOME_CONSTANT">Namespace\<b>SHORT_SOME_CONSTANT</b></a>',
			$this->elementLinkFactory->createForElement($reflectionConstant)
		);
	}


	public function testCreateForProperty()
	{
		$reflectionProperty = Mockery::mock(ReflectionProperty::class);
		$reflectionProperty->shouldReceive('getName')->andReturn('property');
		$reflectionProperty->shouldReceive('getDeclaringClassName')->andReturn('SomeClass');

		$this->assertSame(
			'<a href="property-link-property">SomeClass::<var>$property</var></a>',
			$this->elementLinkFactory->createForElement($reflectionProperty)
		);
	}


	public function testCreateForMethod()
	{
		$reflectionMethod = Mockery::mock(ReflectionMethod::class);
		$reflectionMethod->shouldReceive('getName')->andReturn('method');
		$reflectionMethod->shouldReceive('getDeclaringClassName')->andReturn('SomeClass');

		$this->assertSame(
			'<a href="method-link-method">SomeClass::method()</a>',
			$this->elementLinkFactory->createForElement($reflectionMethod)
		);
	}


	public function testCreateForElementOfUnspecificType()
	{
		$reflectionElement = Mockery::mock(ReflectionElement::class);
		$this->setExpectedException(UnexpectedValueException::class);
		$this->elementLinkFactory->createForElement($reflectionElement);
	}


	public function testCreateForElementWithCssClasses()
	{
		$reflectionClass = Mockery::mock(ReflectionClass::class);
		$reflectionClass->shouldReceive('getName')->andReturn('SomeClass');
		$reflectionClass->shouldReceive('getDeclaringClassName')->andReturn('someElement');

		$this->assertSame(
			'<a href="class-link-SomeClass" class="deprecated">SomeClass</a>',
			$this->elementLinkFactory->createForElement($reflectionClass, ['deprecated'])
		);
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getElementUrlFactoryMock()
	{
		$elementUrlFactoryMock = Mockery::mock(ElementUrlFactory::class);
		$elementUrlFactoryMock->shouldReceive('createForClass')->andReturnUsing(
			function (ReflectionClass $reflectionClass) {
				return 'class-link-' . $reflectionClass->getName();
			}
		);
		$elementUrlFactoryMock->shouldReceive('createForConstant')->andReturnUsing(
			function (ReflectionConstant $reflectionConstant) {
				return 'constant-link-' . $reflectionConstant->getName();
			}
		);
		$elementUrlFactoryMock->shouldReceive('createForFunction')->andReturnUsing(
			function (ReflectionFunction $reflectionFunction) {
				return 'function-link-' . $reflectionFunction->getName();
			}
		);
		$elementUrlFactoryMock->shouldReceive('createForProperty')->andReturnUsing(
			function (ReflectionProperty $reflectionProperty) {
				return 'property-link-' . $reflectionProperty->getName();
			}
		);
		$elementUrlFactoryMock->shouldReceive('createForMethod')->andReturnUsing(
			function (ReflectionMethod $reflectionMethod) {
				return 'method-link-' . $reflectionMethod->getName();
			}
		);
		return $elementUrlFactoryMock;
	}

}

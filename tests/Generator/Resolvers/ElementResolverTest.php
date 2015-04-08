<?php

namespace ApiGen\Tests\Generator\Resolvers;

use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionInterface;
use ApiGen\Tests\MethodInvoker as MI;
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
		$elementReflection = Mockery::mock(ElementReflectionInterface::class);
		$elementReflection->shouldReceive('getName')->andReturn('NiceName');
		$elementReflection->shouldReceive('isDocumented')->andReturn(TRUE);

		$notDocumentedElementReflection = Mockery::mock(ElementReflectionInterface::class);
		$notDocumentedElementReflection->shouldReceive('isDocumented')->andReturn(FALSE);

		$parserStorageMock = Mockery::mock(ParserStorageInterface::class);
		$parserStorageMock->shouldReceive('getClasses')->andReturn([
			'SomeClass' => $elementReflection,
			'SomeNamespace\SomeClass' => $elementReflection,
			'SomeNamespace\SomeOtherClass' => $elementReflection,
			'SomeNotDocumentedClass' => $notDocumentedElementReflection
		]);
		$parserStorageMock->shouldReceive('getConstants')->andReturn([
			'SomeConstant' => $elementReflection,
		]);
		$parserStorageMock->shouldReceive('getFunctions')->andReturn([
			'SomeFunction' => $elementReflection,
		]);
		$this->elementResolver = new ElementResolver($parserStorageMock);
	}


	public function testResolveElement()
	{
		$reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
		$reflectionClassMock->shouldReceive('getNamespaceAliases')->andReturn([]);
		$reflectionClassMock->shouldReceive('getNamespaceName')->andReturn('SomeNamespace');
		$reflectionClassMock->shouldReceive('hasProperty')->andReturn(FALSE);
		$reflectionClassMock->shouldReceive('hasMethod')->with('someMethod')->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('hasMethod')->andReturn(FALSE);
		$reflectionClassMock->shouldReceive('getMethod')->andReturn('someMethod');
		$reflectionClassMock->shouldReceive('hasConstant')->andReturn(FALSE);
		$this->assertNull($this->elementResolver->resolveElement('nonExistingMethod', $reflectionClassMock));

		$this->assertSame('someMethod', $this->elementResolver->resolveElement('someMethod', $reflectionClassMock));
		$this->assertNull($this->elementResolver->resolveElement('string', $reflectionClassMock));
		$this->assertSame($reflectionClassMock, $this->elementResolver->resolveElement('$this', $reflectionClassMock));
		$this->assertSame($reflectionClassMock, $this->elementResolver->resolveElement('self', $reflectionClassMock));
	}


	public function testGetClass()
	{
		$element = $this->elementResolver->getClass('SomeClass');
		$this->assertInstanceOf(ElementReflectionInterface::class, $element);
		$this->assertTrue($element->isDocumented());
		$element = $this->elementResolver->getClass('SomeClass', 'SomeNamespace');
		$this->assertInstanceOf(ElementReflectionInterface::class, $element);
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
		$this->assertInstanceOf(ElementReflectionInterface::class, $element);
		$this->assertTrue($element->isDocumented());
	}


	public function testGetConstantNotExisting()
	{
		$this->assertNull($this->elementResolver->getConstant('NotExistingConstant'));
	}


	public function testGetFunction()
	{
		$element = $this->elementResolver->getFunction('SomeFunction');
		$this->assertInstanceOf(ElementReflectionInterface::class, $element);
		$this->assertTrue($element->isDocumented());
	}


	public function testGetConstantNotFunction()
	{
		$this->assertNull($this->elementResolver->getFunction('NotExistingFunction'));
	}


	public function testIsSimpleType()
	{
		$this->assertTrue(MI::callMethodOnObject($this->elementResolver, 'isSimpleType', ['string']));
		$this->assertTrue(MI::callMethodOnObject($this->elementResolver, 'isSimpleType', ['boolean']));
		$this->assertTrue(MI::callMethodOnObject($this->elementResolver, 'isSimpleType', ['NULL']));
		$this->assertTrue(MI::callMethodOnObject($this->elementResolver, 'isSimpleType', ['']));
		$this->assertFalse(MI::callMethodOnObject($this->elementResolver, 'isSimpleType', ['DateTime']));
	}


	public function testResolveIfParsed()
	{
		$reflectionMethodMock = Mockery::mock(MethodReflectionInterface::class);
		$reflectionMethodMock->shouldReceive('getDeclaringClassName')->andReturnNull();
		$reflectionMethodMock->shouldReceive('getDeclaringFunctionName')->andReturn('SomeFunction');
		$reflectionMethodMock->shouldReceive('getNamespaceName')->andReturnNull();

		$this->assertInstanceOf(
			ElementReflectionInterface::class,
			MI::callMethodOnObject($this->elementResolver, 'resolveIfParsed', ['SomeFunction', $reflectionMethodMock])
		);

		$this->assertInstanceOf(
			ElementReflectionInterface::class,
			MI::callMethodOnObject($this->elementResolver, 'resolveIfParsed', ['SomeClass', $reflectionMethodMock])
		);

		$this->assertInstanceOf(
			ElementReflectionInterface::class,
			MI::callMethodOnObject($this->elementResolver, 'resolveIfParsed', ['SomeConstant', $reflectionMethodMock])
		);

		$this->assertNull(
			MI::callMethodOnObject(
				$this->elementResolver, 'resolveIfParsed', ['NotPresent', $reflectionMethodMock]
			)
		);
	}


	public function testResolveIfInContext()
	{
		$reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
		$reflectionClassMock->shouldReceive('hasProperty')->with('someProperty')->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('hasProperty')->andReturn(FALSE);
		$reflectionClassMock->shouldReceive('getProperty')->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('hasMethod')->with('someMethod')->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('hasMethod')->andReturn(FALSE);
		$reflectionClassMock->shouldReceive('getMethod')->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('hasConstant')->with('someConstant')->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('hasConstant')->andReturn(FALSE);
		$reflectionClassMock->shouldReceive('getConstant')->andReturn(TRUE);

		$this->assertTrue(
			MI::callMethodOnObject($this->elementResolver, 'resolveIfInContext', ['someProperty', $reflectionClassMock])
		);

		$this->assertTrue(
			MI::callMethodOnObject($this->elementResolver, 'resolveIfInContext', ['someMethod', $reflectionClassMock])
		);

		$this->assertTrue(
			MI::callMethodOnObject($this->elementResolver, 'resolveIfInContext', ['someConstant', $reflectionClassMock])
		);

		$this->assertNull(
			MI::callMethodOnObject($this->elementResolver, 'resolveIfInContext', ['someClass', $reflectionClassMock])
		);
	}


	public function testRemoveEndBrackets()
	{
		$this->assertSame(
			'function',
			MI::callMethodOnObject($this->elementResolver, 'removeEndBrackets', ['function()'])
		);
	}


	public function testRemoveStartDollar()
	{
		$this->assertSame(
			'property',
			MI::callMethodOnObject($this->elementResolver, 'removeStartDollar', ['$property'])
		);
	}


	public function testCorrectContextForParameterOrClassMemberWithClass()
	{
		$reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
		$this->assertSame(
			$reflectionClassMock,
			MI::callMethodOnObject(
				$this->elementResolver, 'correctContextForParameterOrClassMember', [$reflectionClassMock]
			)
		);
	}


	public function testCorrectContextForParameterOrClassMemberWithParameter()
	{
		$reflectionParameterMock = Mockery::mock(ParameterReflectionInterface::class);
		$reflectionParameterMock->shouldReceive('getName')->andReturn('NiceName');
		$reflectionParameterMock->shouldReceive('getDeclaringClassName')->andReturn('SomeClass');
		$resolvedElement = MI::callMethodOnObject(
			$this->elementResolver, 'correctContextForParameterOrClassMember', [$reflectionParameterMock]
		);

		$this->assertInstanceOf(ReflectionInterface::class, $resolvedElement);
		$this->assertSame('NiceName', $resolvedElement->getName());
	}


	public function testCorrectContextForParameterOrClassMemberWithParameterAndNoClass()
	{
		$reflectionParameterMock = Mockery::mock(ParameterReflectionInterface::class);
		$reflectionParameterMock->shouldReceive('getDeclaringClassName')->andReturnNull();
		$reflectionParameterMock->shouldReceive('getDeclaringFunctionName')->andReturn('SomeFunction');

		$resolvedElement = MI::callMethodOnObject(
			$this->elementResolver, 'correctContextForParameterOrClassMember', [$reflectionParameterMock]
		);
		$this->assertInstanceOf(ElementReflectionInterface::class, $resolvedElement);
		$this->assertSame('NiceName', $resolvedElement->getName());
	}


	public function testCorrectContextForParameterOrClassMemberWithMethod()
	{
		$reflectionMethodMock = Mockery::mock(MethodReflectionInterface::class);
		$reflectionMethodMock->shouldReceive('getDeclaringClassName')->andReturn('SomeClass');
		$resolvedElement = MI::callMethodOnObject(
			$this->elementResolver, 'correctContextForParameterOrClassMember', [$reflectionMethodMock]
		);

		$this->assertInstanceOf(ElementReflectionInterface::class, $resolvedElement);
		$this->assertSame('NiceName', $resolvedElement->getName());
	}


	public function testResolveContextForClassPropertyInParent()
	{
		$reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
		$reflectionClassMock->shouldReceive('getParentClassName')->andReturn('SomeClass');

		$resolvedElement = MI::callMethodOnObject($this->elementResolver, 'resolveContextForClassProperty', [
			'parent::$start', $reflectionClassMock, 5
		]);

		$this->assertInstanceOf(ElementReflectionInterface::class, $resolvedElement);
	}


	public function testResolveContextForClassPropertyInSelf()
	{
		$reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
		$reflectionClassMock->shouldReceive('getParentClassName')->andReturn('SomeClass');

		$resolvedElement = MI::callMethodOnObject($this->elementResolver, 'resolveContextForClassProperty', [
			'self::$start', $reflectionClassMock, 25
		]);

		$this->assertInstanceOf(ClassReflectionInterface::class, $resolvedElement);
	}


	public function testResolveContextForClassPropertyForNonExisting()
	{
		$reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
		$reflectionClassMock->shouldReceive('getParentClassName')->andReturn('SomeClass');
		$reflectionClassMock->shouldReceive('getNamespaceName')->andReturn('SomeNamespace');
		$reflectionClassMock->shouldReceive('getNamespaceAliases')->andReturn([]);

		$resolvedElement = MI::callMethodOnObject($this->elementResolver, 'resolveContextForClassProperty', [
			'$start', $reflectionClassMock, 25
		]);
		$this->assertNull($resolvedElement);
	}


	public function testResolveContextForSelfProperty()
	{
		$reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
		$reflectionClassMock->shouldReceive('getParentClassName')->andReturn('SomeClass');
		$reflectionClassMock->shouldReceive('getNamespaceName')->andReturn('SomeNamespace');
		$reflectionClassMock->shouldReceive('getNamespaceAliases')->andReturn([]);

		$class = MI::callMethodOnObject(
			$this->elementResolver, 'resolveContextForSelfProperty', ['SomeClass::$property', 9, $reflectionClassMock]
		);
		$this->assertInstanceOf(ElementReflectionInterface::class, $class);

		$class = MI::callMethodOnObject(
			$this->elementResolver,
			'resolveContextForSelfProperty',
			['SomeOtherClass::$property', 14, $reflectionClassMock]
		);
		$this->assertInstanceOf(ElementReflectionInterface::class, $class);

		$this->assertNull(
			MI::callMethodOnObject(
				$this->elementResolver,
				'resolveContextForSelfProperty',
				['NonExistingClass::$property', 14, $reflectionClassMock]
			)
		);
	}


	public function testIsContextUsable()
	{
		$this->assertFalse(
			MI::callMethodOnObject($this->elementResolver, 'isContextUsable', [NULL])
		);

		$reflectionConstantMock = Mockery::mock(ConstantReflectionInterface::class);
		$this->assertFalse(
			MI::callMethodOnObject($this->elementResolver, 'isContextUsable', [$reflectionConstantMock])
		);

		$reflectionFunctionMock = Mockery::mock(FunctionReflectionInterface::class);
		$this->assertFalse(
			MI::callMethodOnObject($this->elementResolver, 'isContextUsable', [$reflectionFunctionMock])
		);

		$reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
		$this->assertTrue(
			MI::callMethodOnObject($this->elementResolver, 'isContextUsable', [$reflectionClassMock])
		);
	}


	/**
	 * @dataProvider getFindElementByNameAndNamespaceData()
	 */
	public function testFindElementByNameAndNamespace($name, $namespace, $expected)
	{
		$elements = ['ApiGen' => 1, 'ApiGen\SomeClass' => 2];
		$this->assertSame($expected,
			MI::callMethodOnObject($this->elementResolver, 'findElementByNameAndNamespace', [$elements, $name, $namespace])
		);
	}


	/**
	 * @return array[]
	 */
	public function getFindElementByNameAndNamespaceData()
	{
		return [
			['ApiGen', '', 1],
			['SomeClass', 'ApiGen', 2],
			['SomeClass', 'ApiGen\Generator', NULL],
			['\\ApiGen', '', 1]
		];
	}

}

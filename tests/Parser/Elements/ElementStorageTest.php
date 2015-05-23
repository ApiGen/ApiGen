<?php

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Contracts\Parser\Configuration\ParserConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Parser\Elements\GroupSorter;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionElement;
use ApiGen\Parser\Tests\MethodInvoker;
use Mockery;
use Nette\Object;
use PHPUnit_Framework_TestCase;
use TokenReflection\Php\IReflection;


class ElementStorageTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ClassReflectionInterface
	 */
	private $reflectionClass;


	public function testEnsureCategorization()
	{
		$configurationMock = Mockery::mock(ParserConfigurationInterface::class);
		$configurationMock->shouldReceive('areNamespacesEnabled')->andReturn(TRUE);
		$configurationMock->shouldReceive('arePackagesEnabled')->andReturn(FALSE);
		$elementStorage = $this->prepareElementStorage($configurationMock);

		MethodInvoker::callMethodOnObject($elementStorage, 'ensureCategorization');

		$this->assertCount(1, $elementStorage->getClasses());
		$this->assertCount(1, $elementStorage->getTraits());
		$this->assertCount(1, $elementStorage->getInterfaces());
		$this->assertCount(1, $elementStorage->getExceptions());
		$this->assertCount(4, $elementStorage->getClassElements());

		$this->assertCount(1, $elementStorage->getFunctions());
		$this->assertCount(1, $elementStorage->getConstants());

		$this->assertCount(1, $elementStorage->getNamespaces());
		$this->assertCount(0, $elementStorage->getPackages());
	}


	public function testEnsureCategorizationPackagesEnabled()
	{
		$configurationMock = Mockery::mock(ParserConfigurationInterface::class);
		$configurationMock->shouldReceive('areNamespacesEnabled')->andReturn(FALSE);
		$configurationMock->shouldReceive('arePackagesEnabled')->andReturn(TRUE);

		$elementStorage = $this->prepareElementStorage($configurationMock);
		MethodInvoker::callMethodOnObject($elementStorage, 'ensureCategorization');

		$this->assertCount(0, $elementStorage->getNamespaces());
		$this->assertCount(1, $elementStorage->getPackages());
	}


	public function testEnsureCategorizationPackagesNorNamespacesEnabled()
	{
		$configurationMock = Mockery::mock(ParserConfigurationInterface::class);
		$configurationMock->shouldReceive('areNamespacesEnabled')->andReturn(FALSE);
		$configurationMock->shouldReceive('arePackagesEnabled')->andReturn(FALSE);
		$elementStorage = $this->prepareElementStorage($configurationMock);

		MethodInvoker::callMethodOnObject($elementStorage, 'ensureCategorization');

		$this->assertCount(0, $elementStorage->getNamespaces());
		$this->assertCount(0, $elementStorage->getPackages());
	}


	public function testLoadUsesToReferencedElementUsedBy()
	{
		$configurationMock = Mockery::mock(ParserConfigurationInterface::class);
		$configurationMock->shouldReceive('areNamespacesEnabled')->andReturn(TRUE);
		$configurationMock->shouldReceive('arePackagesEnabled')->andReturn(FALSE);
		$elementStorage = $this->prepareElementStorage($configurationMock);

		$reflectionElementMock = Mockery::mock(ReflectionElement::class);
		$reflectionElementMock->shouldReceive('getAnnotation')->with('uses')->once()->andReturnNull();
		$reflectionElementMock->shouldReceive('getAnnotation')->with('uses')->twice()->andReturn(['ApiGen\ApiGen']);
		$reflectionElementMock->shouldReceive('getPrettyName')->andReturn('PrettyName');

		$this->assertFalse($this->reflectionClass->hasAnnotation('usedby'));
		MethodInvoker::callMethodOnObject(
			$elementStorage, 'loadUsesToReferencedElementUsedBy', [$reflectionElementMock]
		);
		$this->assertFalse($this->reflectionClass->hasAnnotation('usedby'));

		MethodInvoker::callMethodOnObject(
			$elementStorage, 'loadUsesToReferencedElementUsedBy', [$reflectionElementMock]
		);
		$this->assertTrue($this->reflectionClass->hasAnnotation('usedby'));
	}


	/**
	 * @return ElementStorage
	 */
	private function prepareElementStorage($configurationMock)
	{
		$parserStorageMock = Mockery::mock(ParserStorageInterface::class);
		$parserStorageMock->shouldReceive('getTypes')->andReturn(['classes', 'functions', 'constants']);
		$parserStorageMock->shouldReceive('getElementsByType')->with('classes')
			->andReturn($this->getReflectionClassMocks());

		$parserStorageMock->shouldReceive('getElementsByType')->with('functions')
			->andReturn([$this->getReflectionFunctionMock()]);

		$parserStorageMock->shouldReceive('getElementsByType')->with('constants')
			->andReturn([$this->getReflectionConstantMock()]);

		$groupSorterMock = Mockery::mock(GroupSorter::class);
		$groupSorterMock->shouldReceive('sort')->andReturnUsing(function ($elements) {
			return $elements;
		});

		$iReflectionClassMock = Mockery::mock(IReflection::class, Object::class);
		$iReflectionClassMock->shouldReceive('getAnnotations')->andReturn([]);

		$this->reflectionClass = new ReflectionClass($iReflectionClassMock);
		$elementResolverMock = Mockery::mock(ElementResolverInterface::class);
		$elementResolverMock->shouldReceive('resolveElement')->andReturn($this->reflectionClass);

		return new ElementStorage(
			$parserStorageMock, $configurationMock, $groupSorterMock, $elementResolverMock
		);
	}


	/**
	 * @return ReflectionClass[]
	 */
	private function getReflectionClassMocks()
	{
		$classes = [];
		$reflectionClassMock = $this->getReflectionClassMock();
		$reflectionClassMock->shouldReceive('isDocumented')->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('isInterface')->andReturn(FALSE);
		$reflectionClassMock->shouldReceive('isTrait')->andReturn(FALSE);
		$reflectionClassMock->shouldReceive('isException')->andReturn(FALSE);
		$classes[] = $reflectionClassMock;

		$reflectionClassMock2 = $this->getReflectionClassMock();
		$reflectionClassMock2->shouldReceive('isDocumented')->andReturn(FALSE);
		$classes[] = $reflectionClassMock2;

		$reflectionClassMock3 = $this->getReflectionClassMock();
		$reflectionClassMock3->shouldReceive('isDocumented')->andReturn(TRUE);
		$reflectionClassMock3->shouldReceive('isInterface')->andReturn(TRUE);
		$reflectionClassMock3->shouldReceive('isTrait')->andReturn(FALSE);
		$reflectionClassMock3->shouldReceive('isException')->andReturn(FALSE);
		$classes[] = $reflectionClassMock3;

		$reflectionClassMock4 = $this->getReflectionClassMock();
		$reflectionClassMock4->shouldReceive('isDocumented')->andReturn(TRUE);
		$reflectionClassMock4->shouldReceive('isInterface')->andReturn(FALSE);
		$reflectionClassMock4->shouldReceive('isTrait')->andReturn(TRUE);
		$reflectionClassMock4->shouldReceive('isException')->andReturn(FALSE);
		$classes[] = $reflectionClassMock4;

		$reflectionClassMock5 = $this->getReflectionClassMock();
		$reflectionClassMock5->shouldReceive('isDocumented')->andReturn(TRUE);
		$reflectionClassMock5->shouldReceive('isInterface')->andReturn(FALSE);
		$reflectionClassMock5->shouldReceive('isTrait')->andReturn(FALSE);
		$reflectionClassMock5->shouldReceive('isException')->andReturn(TRUE);
		$classes[] = $reflectionClassMock5;
		return $classes;
	}


	/**
	 * @return Mockery\MockInterface|ClassReflectionInterface
	 */
	private function getReflectionClassMock()
	{
		$reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
		$reflectionClassMock->shouldReceive('getPseudoPackageName')->andReturn('SomePackage');
		$reflectionClassMock->shouldReceive('getPseudoNamespaceName')->andReturn('SomeNamespace');
		$reflectionClassMock->shouldReceive('getShortName')->andReturn('SomeShortClass');
		$reflectionClassMock->shouldReceive('getOwnMethods')->andReturn([]);
		$reflectionClassMock->shouldReceive('getOwnConstants')->andReturn([]);
		$reflectionClassMock->shouldReceive('getOwnProperties')->andReturn([]);
		$reflectionClassMock->shouldReceive('getAnnotation')->andReturn([]);
		return $reflectionClassMock;
	}


	/**
	 * @return Mockery\MockInterface|FunctionReflectionInterface
	 */
	private function getReflectionFunctionMock()
	{
		$reflectionFunctionMock = Mockery::mock(FunctionReflectionInterface::class);
		$reflectionFunctionMock->shouldReceive('isDocumented')->andReturn(TRUE);
		$reflectionFunctionMock->shouldReceive('getPseudoPackageName')->andReturn('SomePackage');
		$reflectionFunctionMock->shouldReceive('getPseudoNamespaceName')->andReturn('SomeNamespace');
		$reflectionFunctionMock->shouldReceive('getShortName')->andReturn('SomeShortClass');
		$reflectionFunctionMock->shouldReceive('getAnnotation')->andReturn([]);
		return $reflectionFunctionMock;
	}


	/**
	 * @return Mockery\MockInterface|ConstantReflectionInterface
	 */
	private function getReflectionConstantMock()
	{
		$reflectionConstantMock = Mockery::mock(ConstantReflectionInterface::class);
		$reflectionConstantMock->shouldReceive('isDocumented')->andReturn(TRUE);
		$reflectionConstantMock->shouldReceive('getPseudoPackageName')->andReturn('SomePackage');
		$reflectionConstantMock->shouldReceive('getPseudoNamespaceName')->andReturn('SomeNamespace');
		$reflectionConstantMock->shouldReceive('getShortName')->andReturn('SomeShortClass');
		$reflectionConstantMock->shouldReceive('getAnnotation')->andReturn([]);
		return $reflectionConstantMock;
	}

}

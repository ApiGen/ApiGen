<?php

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Generator\TemplateGenerators\TreeGenerator;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Tests\MethodInvoker as MI;
use ArrayObject;
use Mockery;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;


class TreeGeneratorTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var TreeGenerator
	 */
	private $treeGenerator;


	protected function setUp()
	{
		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
		$configurationMock->shouldReceive('getOption')->with(CO::TREE)->once()->andReturn(TRUE);
		$configurationMock->shouldReceive('getOption')->with(CO::TREE)->once()->andReturn(FALSE);

		$templateFactoryMock = Mockery::mock('ApiGen\Templating\TemplateFactory');

		$templateMock = Mockery::mock('ApiGen\Templating\Template');
		$templateMock->shouldReceive('setParameters');
		$templateMock->shouldReceive('save');
		$templateFactoryMock->shouldReceive('createForType')->andReturn($templateMock);

		$elementStorageMock = Mockery::mock('ApiGen\Parser\Elements\ElementStorage');

		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$reflectionClassMock->shouldReceive('isMain')->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('isDocumented')->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('getName')->andReturn('SomeClass');
		$reflectionClassMock->shouldReceive('getParentClassName')->andReturn('');
		$reflectionClassMock->shouldReceive('getParentClasses')->andReturn([]);

		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
		$parserResultMock->shouldReceive('getClasses')->andReturn(new ArrayObject([$reflectionClassMock]));

		$this->treeGenerator = new TreeGenerator(
			$configurationMock, $templateFactoryMock, $elementStorageMock, $parserResultMock
		);
	}


	public function testGenerate()
	{
		$this->treeGenerator->generate();
	}


	public function testIsAllowed()
	{
		$this->assertTrue($this->treeGenerator->isAllowed());
		$this->assertFalse($this->treeGenerator->isAllowed());
	}


	public function testCanBeProcessed()
	{
		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$reflectionClassMock->shouldReceive('isMain')->once()->andReturn(FALSE);
		$reflectionClassMock->shouldReceive('isMain')->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('isDocumented')->once()->andReturn(FALSE);
		$reflectionClassMock->shouldReceive('isDocumented')->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('getName')->andReturn('someClass');

		$this->assertFalse(MI::callMethodOnObject($this->treeGenerator, 'canBeProcessed', [$reflectionClassMock]));
		$this->assertFalse(MI::callMethodOnObject($this->treeGenerator, 'canBeProcessed', [$reflectionClassMock]));
		$this->assertTrue(MI::callMethodOnObject($this->treeGenerator, 'canBeProcessed', [$reflectionClassMock]));

		MI::callMethodOnObject($this->treeGenerator, 'addToTreeByTypeAndName', ['type', 'someClass']);
		$this->assertFalse(MI::callMethodOnObject($this->treeGenerator, 'canBeProcessed', [$reflectionClassMock]));
	}


	public function testAddToTreeByReflection()
	{
		$reflectionClassParentMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$reflectionClassParentMock->shouldReceive('getName')->andReturn('ParentClassName');

		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$reflectionClassMock->shouldReceive('getName')->andReturn('someClass');
		$reflectionClassMock->shouldReceive('getParentClassName')->once()->andReturn(NULL);
		$reflectionClassMock->shouldReceive('getParentClassName')->andReturn('ParentClassName');
		$reflectionClassMock->shouldReceive('getParentClasses')->andReturn([0 => $reflectionClassParentMock]);
		$reflectionClassMock->shouldReceive('isInterface')->once()->andReturn(TRUE);

		MI::callMethodOnObject($this->treeGenerator, 'addToTreeByReflection', [$reflectionClassMock]);
		$processed = PHPUnit_Framework_Assert::getObjectAttribute($this->treeGenerator, 'processed');
		$this->arrayHasKey('someClass', $processed);

		MI::callMethodOnObject($this->treeGenerator, 'addToTreeByReflection', [$reflectionClassMock]);
		$processed = PHPUnit_Framework_Assert::getObjectAttribute($this->treeGenerator, 'processed');
		$this->arrayHasKey('ParentClassName', $processed);
	}


	public function testGetTypeByReflection()
	{
		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$reflectionClassMock->shouldReceive('isInterface')->once()->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('isInterface')->andReturn(FALSE);
		$reflectionClassMock->shouldReceive('isTrait')->once()->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('isTrait')->andReturn(FALSE);
		$reflectionClassMock->shouldReceive('isException')->once()->andReturn(TRUE);
		$reflectionClassMock->shouldReceive('isException')->andReturn(FALSE);

		$this->assertSame(
			Elements::INTERFACES,
			MI::callMethodOnObject($this->treeGenerator, 'getTypeByReflection', [$reflectionClassMock])
		);

		$this->assertSame(
			Elements::TRAITS,
			MI::callMethodOnObject($this->treeGenerator, 'getTypeByReflection', [$reflectionClassMock])
		);

		$this->assertSame(
			Elements::EXCEPTIONS,
			MI::callMethodOnObject($this->treeGenerator, 'getTypeByReflection', [$reflectionClassMock])
		);

		$this->assertSame(
			Elements::CLASSES,
			MI::callMethodOnObject($this->treeGenerator, 'getTypeByReflection', [$reflectionClassMock])
		);
	}


	public function testAddToTreeByTypeAndName()
	{
		MI::callMethodOnObject($this->treeGenerator, 'addToTreeByTypeAndName', ['type', 'name']);

		$treeStorage = PHPUnit_Framework_Assert::getObjectAttribute($this->treeGenerator, 'treeStorage');
		$this->assertArrayHasKey('type', $treeStorage);
		$this->assertArrayHasKey('name', $treeStorage['type']);

		$processed = PHPUnit_Framework_Assert::getObjectAttribute($this->treeGenerator, 'processed');
		$this->assertArrayHasKey('name', $processed);
	}


	public function testSortTreeStorageElements()
	{
		MI::callMethodOnObject($this->treeGenerator, 'addToTreeByTypeAndName', ['type', 'b']);
		MI::callMethodOnObject($this->treeGenerator, 'addToTreeByTypeAndName', ['type', 'a']);

		$originalTreeStorage = PHPUnit_Framework_Assert::getObjectAttribute($this->treeGenerator, 'treeStorage');
		$this->assertSame(['b' => [], 'a' => []], $originalTreeStorage['type']);

		MI::callMethodOnObject($this->treeGenerator, 'sortTreeStorageElements');
		$originalTreeStorage = PHPUnit_Framework_Assert::getObjectAttribute($this->treeGenerator, 'treeStorage');
		$this->assertSame(['a' => [], 'b' => []], $originalTreeStorage['type']);
	}

}

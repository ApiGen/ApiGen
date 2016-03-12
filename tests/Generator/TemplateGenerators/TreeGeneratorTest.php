<?php

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Generator\TemplateGenerators\TreeGenerator;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Parser\ParserResult;
use ApiGen\Parser\ParserStorage;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;
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
        $configurationMock = Mockery::mock(Configuration::class);
        $configurationMock->shouldReceive('getOption')->with(CO::TREE)->once()->andReturn(true);
        $configurationMock->shouldReceive('getOption')->with(CO::TREE)->once()->andReturn(false);

        $templateFactoryMock = Mockery::mock(TemplateFactory::class);

        $templateMock = Mockery::mock(Template::class);
        $templateMock->shouldReceive('setParameters');
        $templateMock->shouldReceive('save');
        $templateFactoryMock->shouldReceive('createForType')->andReturn($templateMock);

        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $reflectionClassMock->shouldReceive('isMain')->andReturn(true);
        $reflectionClassMock->shouldReceive('isDocumented')->andReturn(true);
        $reflectionClassMock->shouldReceive('getName')->andReturn('SomeClass');
        $reflectionClassMock->shouldReceive('getParentClassName')->andReturn('');
        $reflectionClassMock->shouldReceive('getParentClasses')->andReturn([]);
        $reflectionClassMock->shouldReceive('isInterface')->andReturn(false);
        $reflectionClassMock->shouldReceive('isTrait')->andReturn(false);
        $reflectionClassMock->shouldReceive('isException')->andReturn(false);

        $parserStorageMock = Mockery::mock(ParserStorageInterface::class);
        $parserStorageMock->shouldReceive('getClasses')->andReturn(new ArrayObject([$reflectionClassMock]));

        $this->treeGenerator = new TreeGenerator(
            $configurationMock,
            $templateFactoryMock,
            $parserStorageMock
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
        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $reflectionClassMock->shouldReceive('isMain')->once()->andReturn(false);
        $reflectionClassMock->shouldReceive('isMain')->andReturn(true);
        $reflectionClassMock->shouldReceive('isDocumented')->once()->andReturn(false);
        $reflectionClassMock->shouldReceive('isDocumented')->andReturn(true);
        $reflectionClassMock->shouldReceive('getName')->andReturn('someClass');

        $this->assertFalse(MI::callMethodOnObject($this->treeGenerator, 'canBeProcessed', [$reflectionClassMock]));
        $this->assertFalse(MI::callMethodOnObject($this->treeGenerator, 'canBeProcessed', [$reflectionClassMock]));
        $this->assertTrue(MI::callMethodOnObject($this->treeGenerator, 'canBeProcessed', [$reflectionClassMock]));

        MI::callMethodOnObject($this->treeGenerator, 'addToTreeByTypeAndName', ['type', 'someClass']);
        $this->assertFalse(MI::callMethodOnObject($this->treeGenerator, 'canBeProcessed', [$reflectionClassMock]));
    }


    public function testAddToTreeByReflection()
    {
        $reflectionClassParentMock = Mockery::mock(ClassReflectionInterface::class);
        $reflectionClassParentMock->shouldReceive('getName')->andReturn('ParentClassName');
        $reflectionClassParentMock->shouldReceive('isInterface')->andReturn(true);

        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $reflectionClassMock->shouldReceive('getName')->andReturn('someClass');
        $reflectionClassMock->shouldReceive('getParentClassName')->andReturn('ParentClassName');
        $reflectionClassMock->shouldReceive('getParentClasses')->andReturn([0 => $reflectionClassParentMock]);
        $reflectionClassMock->shouldReceive('isInterface')->once()->andReturn(true);

        MI::callMethodOnObject($this->treeGenerator, 'addToTreeByReflection', [$reflectionClassMock]);
        $processed = PHPUnit_Framework_Assert::getObjectAttribute($this->treeGenerator, 'processed');
        $this->arrayHasKey('someClass', $processed);

        MI::callMethodOnObject($this->treeGenerator, 'addToTreeByReflection', [$reflectionClassMock]);
        $processed = PHPUnit_Framework_Assert::getObjectAttribute($this->treeGenerator, 'processed');
        $this->arrayHasKey('ParentClassName', $processed);
    }


    public function testGetTypeByReflection()
    {
        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $reflectionClassMock->shouldReceive('isInterface')->once()->andReturn(true);
        $reflectionClassMock->shouldReceive('isInterface')->andReturn(false);
        $reflectionClassMock->shouldReceive('isTrait')->once()->andReturn(true);
        $reflectionClassMock->shouldReceive('isTrait')->andReturn(false);
        $reflectionClassMock->shouldReceive('isException')->once()->andReturn(true);
        $reflectionClassMock->shouldReceive('isException')->andReturn(false);

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

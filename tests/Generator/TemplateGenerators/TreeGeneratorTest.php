<?php

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Parser\Elements\ElementsInterface;
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
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class TreeGeneratorTest extends TestCase
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
        $reflectionClassMock->shouldReceive('isInterface')->andReturn(false);
        $reflectionClassMock->shouldReceive('isTrait')->andReturn(false);
        $reflectionClassMock->shouldReceive('isException')->andReturn(false);
        $reflectionClassMock->shouldReceive('isMain')->andReturn(true);
        $reflectionClassMock->shouldReceive('isDocumented')->andReturn(true);
        $reflectionClassMock->shouldReceive('getName')->andReturn('SomeClass');
        $reflectionClassMock->shouldReceive('getParentClassName')->andReturn('');
        $reflectionClassMock->shouldReceive('getParentClasses')->andReturn([]);

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
    }


    public function testCanBeProcessedNotDocumented()
    {
        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $reflectionClassMock->shouldReceive('isMain')->andReturn(true);
        $reflectionClassMock->shouldReceive('isDocumented')->andReturn(false);

        $this->assertFalse(MI::callMethodOnObject($this->treeGenerator, 'canBeProcessed', [$reflectionClassMock]));
    }


    public function testCanBeProcessedDuplicate()
    {
        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $reflectionClassMock->shouldReceive('isMain')->andReturn(true);
        $reflectionClassMock->shouldReceive('isDocumented')->andReturn(true);
        $reflectionClassMock->shouldReceive('isInterface')->andReturn(false);
        $reflectionClassMock->shouldReceive('isTrait')->andReturn(false);
        $reflectionClassMock->shouldReceive('isException')->andReturn(false);
        $reflectionClassMock->shouldReceive('getName')->andReturn('MyClass');
        $reflectionClassMock->shouldReceive('getParentClassName')->andReturn(null);

        $this->assertTrue(MI::callMethodOnObject($this->treeGenerator, 'canBeProcessed', [$reflectionClassMock]));
        MI::callMethodOnObject($this->treeGenerator, 'addToTreeByReflection', [$reflectionClassMock]);
        $this->assertFalse(MI::callMethodOnObject($this->treeGenerator, 'canBeProcessed', [$reflectionClassMock]));
    }


    public function testAddToTreeByReflection()
    {
        $reflectionClassParentMock = Mockery::mock(ClassReflectionInterface::class);
        $reflectionClassParentMock->shouldReceive('getName')->andReturn('ParentClassName');

        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $reflectionClassMock->shouldReceive('getName')->andReturn('someClass');
        $reflectionClassMock->shouldReceive('isInterface')->andReturn(false);
        $reflectionClassMock->shouldReceive('isTrait')->andReturn(false);
        $reflectionClassMock->shouldReceive('isException')->andReturn(false);
        $reflectionClassMock->shouldReceive('getParentClassName')->once()->andReturn(null);
        $reflectionClassMock->shouldReceive('getParentClassName')->andReturn('ParentClassName');
        $reflectionClassMock->shouldReceive('getParentClasses')->andReturn([0 => $reflectionClassParentMock]);

        MI::callMethodOnObject($this->treeGenerator, 'addToTreeByReflection', [$reflectionClassMock]);
        MI::callMethodOnObject($this->treeGenerator, 'addToTreeByReflection', [$reflectionClassMock]);

        $this->assertAttributeSame(
            ['someClass' => true, 'ParentClassName' => true],
            'processed',
            $this->treeGenerator
        );

        $expected = [
            ElementsInterface::CLASSES => [
                'ParentClassName' => ['someClass' => []],
                'someClass' => [],
            ],
            ElementsInterface::INTERFACES => [],
            ElementsInterface::TRAITS => [],
            ElementsInterface::EXCEPTIONS => []
        ];
        $this->assertAttributeEquals($expected, 'treeStorage', $this->treeGenerator);
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
}

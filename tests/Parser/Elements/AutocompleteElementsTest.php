<?php

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Parser\Elements\AutocompleteElements;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionConstant;
use ApiGen\Parser\Reflection\ReflectionFunction;
use ApiGen\Parser\Reflection\ReflectionMethod;
use ApiGen\Parser\Reflection\ReflectionProperty;
use Mockery;
use PHPUnit_Framework_TestCase;

class AutocompleteElementsTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var AutocompleteElements
     */
    private $autocompleteElements;


    protected function setUp()
    {
        $classReflectionMock = Mockery::mock(ReflectionClass::class);
        $classReflectionMock->shouldReceive('getPrettyName')->andReturn('ClassPrettyName');
        $classReflectionMock->shouldReceive('getOwnConstants')->andReturn([]);

        $methodReflection = Mockery::mock(ReflectionMethod::class);
        $methodReflection->shouldReceive('getPrettyName')->andReturn('ClassPrettyName::methodName');
        $classReflectionMock->shouldReceive('getOwnMethods')->andReturn([$methodReflection]);

        $propertyReflection = Mockery::mock(ReflectionProperty::class);
        $propertyReflection->shouldReceive('getPrettyName')->andReturn('ClassPrettyName::$propertyName');
        $classReflectionMock->shouldReceive('getOwnProperties')->andReturn([$propertyReflection]);

        $constantReflectionMock = Mockery::mock(ReflectionConstant::class);
        $constantReflectionMock->shouldReceive('getPrettyName')->andReturn('ConstantPrettyName');

        $functionReflectionMock = Mockery::mock(ReflectionFunction::class);
        $functionReflectionMock->shouldReceive('getPrettyName')->andReturn('FunctionPrettyName');

        $elementsStorageMock = Mockery::mock(ElementStorage::class);
        $elementsStorageMock->shouldReceive('getElements')->andReturn([
            'classes' => [$classReflectionMock],
            'constants' => [$constantReflectionMock],
            'functions' => [$functionReflectionMock]
        ]);

        $this->autocompleteElements = new AutocompleteElements($elementsStorageMock);
    }


    public function testGetElementsClasses()
    {
        $elements = $this->autocompleteElements->getElements();
        $this->assertSame([
            ['c', 'ClassPrettyName'],
            ['p', 'ClassPrettyName::$propertyName'],
            ['m', 'ClassPrettyName::methodName'],
            ['co', 'ConstantPrettyName'],
            ['f', 'FunctionPrettyName'],
        ], $elements);
    }
}

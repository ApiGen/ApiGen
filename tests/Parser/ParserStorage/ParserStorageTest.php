<?php

namespace ApiGen\Parser\Tests\ParserStorage;

use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Parser\ParserStorage;
use ApiGen\Parser\Tests\MethodInvoker;
use ArrayObject;
use Exception;
use Mockery;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;

class ParserStorageTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;


    protected function setUp()
    {
        $this->parserStorage = new ParserStorage;
    }


    public function testDefaultsOnConstruct()
    {
        $this->assertInstanceOf(ArrayObject::class, $this->parserStorage->getClasses());
        $this->assertInstanceOf(ArrayObject::class, $this->parserStorage->getConstants());
        $this->assertInstanceOf(ArrayObject::class, $this->parserStorage->getFunctions());
        $this->assertInstanceOf(ArrayObject::class, PHPUnit_Framework_Assert::getObjectAttribute(
            $this->parserStorage,
            'internalClasses'
        ));
        $this->assertInstanceOf(ArrayObject::class, PHPUnit_Framework_Assert::getObjectAttribute(
            $this->parserStorage,
            'tokenizedClasses'
        ));
    }


    public function testSettersAndGetters()
    {
        $classes = new ArrayObject([1]);
        $this->parserStorage->setClasses($classes);
        $this->assertSame($classes, $this->parserStorage->getClasses());

        $constants = new ArrayObject([2]);
        $this->parserStorage->setConstants($constants);
        $this->assertSame($constants, $this->parserStorage->getConstants());

        $functions = new ArrayObject([3]);
        $this->parserStorage->setFunctions($functions);
        $this->assertSame($functions, $this->parserStorage->getFunctions());
    }


    public function testGetElementsByType()
    {
        $classes = new ArrayObject([1]);
        $this->parserStorage->setClasses($classes);
        $this->assertSame($classes, $this->parserStorage->getElementsByType(Elements::CLASSES));

        $constants = new ArrayObject([2]);
        $this->parserStorage->setConstants($constants);
        $this->assertSame($constants, $this->parserStorage->getElementsByType(Elements::CONSTANTS));

        $functions = new ArrayObject([3]);
        $this->parserStorage->setFunctions($functions);
        $this->assertSame($functions, $this->parserStorage->getElementsByType(Elements::FUNCTIONS));

        $internalClasses = new ArrayObject([4]);
        $this->parserStorage->setInternalClasses($internalClasses);
        $this->assertSame($internalClasses, PHPUnit_Framework_Assert::getObjectAttribute(
            $this->parserStorage,
            'internalClasses'
        ));

        $tokenizedClasses = new ArrayObject([5]);
        $this->parserStorage->setTokenizedClasses($tokenizedClasses);
        $this->assertSame($tokenizedClasses, PHPUnit_Framework_Assert::getObjectAttribute(
            $this->parserStorage,
            'tokenizedClasses'
        ));
    }


    public function testGetElementsByTypeWithUnknownType()
    {
        $this->setExpectedException(Exception::class);
        $this->parserStorage->getElementsByType('elements');
    }


    public function testGetTypes()
    {
        $this->assertSame(
            [Elements::CLASSES, Elements::CONSTANTS, Elements::FUNCTIONS],
            $this->parserStorage->getTypes()
        );
    }


    public function testGetDocumentedStats()
    {
        $this->parserStorage->setInternalClasses($this->getReflectionElementsArrayObject());
        $documentedStats = $this->parserStorage->getDocumentedStats();
        $this->assertInternalType('array', $documentedStats);
        $this->assertArrayHasKey('classes', $documentedStats);
        $this->assertArrayHasKey('constants', $documentedStats);
        $this->assertArrayHasKey('functions', $documentedStats);
        $this->assertArrayHasKey('internalClasses', $documentedStats);
        $this->assertSame(1, $documentedStats['internalClasses']);
    }


    public function testGetDocumentedElementsCount()
    {
        $reflectionElements = $this->getReflectionElementsArrayObject();
        $this->assertSame(1, MethodInvoker::callMethodOnObject(
            $this->parserStorage,
            'getDocumentedElementsCount',
            [$reflectionElements]
        ));
    }


    /**
     * @return ArrayObject
     */
    private function getReflectionElementsArrayObject()
    {
        $reflectionElementMock = Mockery::mock(ElementReflectionInterface::class);
        $reflectionElementMock->shouldReceive('isDocumented')->andReturn(true);

        $reflectionElementMock2 = Mockery::mock(ElementReflectionInterface::class);
        $reflectionElementMock2->shouldReceive('isDocumented')->andReturn(false);

        $reflectionElements = new ArrayObject([$reflectionElementMock, $reflectionElementMock2]);
        return $reflectionElements;
    }
}

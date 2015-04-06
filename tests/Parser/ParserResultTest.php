<?php

namespace ApiGen\Tests\Parser;

use ApiGen\Parser\Elements\Elements;
use ApiGen\Parser\ParserResult;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Tests\MethodInvoker;
use ArrayObject;
use Exception;
use Mockery;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;


class ParserResultTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ParserResult
	 */
	private $parserResult;


	protected function setUp()
	{
		$this->parserResult = new ParserResult;
	}


	public function testDefaultsOnConstruct()
	{
		$this->assertInstanceOf(ArrayObject::class, $this->parserResult->getClasses());
		$this->assertInstanceOf(ArrayObject::class, $this->parserResult->getConstants());
		$this->assertInstanceOf(ArrayObject::class, $this->parserResult->getFunctions());
		$this->assertInstanceOf(ArrayObject::class, PHPUnit_Framework_Assert::getObjectAttribute(
			$this->parserResult, 'internalClasses'
		));
		$this->assertInstanceOf(ArrayObject::class, PHPUnit_Framework_Assert::getObjectAttribute(
			$this->parserResult, 'tokenizedClasses'
		));
	}


	public function testSettersAndGetters()
	{
		$classes = new ArrayObject([1]);
		$this->parserResult->setClasses($classes);
		$this->assertSame($classes, $this->parserResult->getClasses());

		$constants = new ArrayObject([2]);
		$this->parserResult->setConstants($constants);
		$this->assertSame($constants, $this->parserResult->getConstants());

		$functions = new ArrayObject([3]);
		$this->parserResult->setFunctions($functions);
		$this->assertSame($functions, $this->parserResult->getFunctions());
	}


	public function testGetElementsByType()
	{
		$classes = new ArrayObject([1]);
		$this->parserResult->setClasses($classes);
		$this->assertSame($classes, $this->parserResult->getElementsByType(Elements::CLASSES));

		$constants = new ArrayObject([2]);
		$this->parserResult->setConstants($constants);
		$this->assertSame($constants, $this->parserResult->getElementsByType(Elements::CONSTANTS));

		$functions = new ArrayObject([3]);
		$this->parserResult->setFunctions($functions);
		$this->assertSame($functions, $this->parserResult->getElementsByType(Elements::FUNCTIONS));

		$internalClasses = new ArrayObject([4]);
		$this->parserResult->setInternalClasses($internalClasses);
		$this->assertSame($internalClasses, PHPUnit_Framework_Assert::getObjectAttribute(
			$this->parserResult, 'internalClasses'
		));

		$tokenizedClasses = new ArrayObject([5]);
		$this->parserResult->setTokenizedClasses($tokenizedClasses);
		$this->assertSame($tokenizedClasses, PHPUnit_Framework_Assert::getObjectAttribute(
			$this->parserResult, 'tokenizedClasses'
		));
	}


	public function testGetElementsByTypeWithUnknownType()
	{
		$this->setExpectedException(Exception::class);
		$this->parserResult->getElementsByType('elements');
	}


	public function testGetTypes()
	{
		$this->assertSame(
			[Elements::CLASSES, Elements::CONSTANTS, Elements::FUNCTIONS],
			$this->parserResult->getTypes()
		);
	}


	public function testGetDocumentedStats()
	{
		$this->parserResult->setInternalClasses($this->getReflectionElementsArrayObject());
		$documentedStats = $this->parserResult->getDocumentedStats();
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
			$this->parserResult, 'getDocumentedElementsCount', [$reflectionElements]
		));
	}


	/**
	 * @return ArrayObject
	 */
	private function getReflectionElementsArrayObject()
	{
		$reflectionElementMock = Mockery::mock(ReflectionElement::class);
		$reflectionElementMock->shouldReceive('isDocumented')->andReturn(TRUE);

		$reflectionElementMock2 = Mockery::mock(ReflectionElement::class);
		$reflectionElementMock2->shouldReceive('isDocumented')->andReturn(FALSE);

		$reflectionElements = new ArrayObject([$reflectionElementMock, $reflectionElementMock2]);
		return $reflectionElements;
	}

}

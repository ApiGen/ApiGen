<?php

namespace ApiGen\Parser\Tests\ParserStorage;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Parser\Tests\ContainerAwareTestCase;
use ApiGen\Parser\Tests\ParserStorageImplementersSource\ChildInterface;
use ApiGen\Parser\Tests\ParserStorageImplementersSource\ParentInterface;
use ApiGen\Parser\Tests\ParserStorageImplementersSource\SomeClass;
use Nette\Utils\Finder;


class ImplementersTest extends ContainerAwareTestCase
{

	/**
	 * @var ParserStorageInterface
	 */
	private $parserStorage;

	/**
	 * @var ClassReflectionInterface
	 */
	private $parentInterfaceReflection;


	protected function setUp()
	{
		$finder = Finder::find('*')->in(__DIR__ . '/ImplementersSource');
		$files = iterator_to_array($finder->getIterator());

		/** @var ParserInterface $parser */
		$parser = $this->container->getByType(ParserInterface::class);
		$parser->parse($files);

		$this->parserStorage = $this->container->getByType(ParserStorageInterface::class);
		$classes = $this->parserStorage->getClasses();

		$this->parentInterfaceReflection = $classes[ParentInterface::class];
	}


	public function testGetDirectImplementersOfInterface()
	{
		$implementers = $this->parserStorage->getDirectImplementersOfInterface($this->parentInterfaceReflection);
		$this->assertCount(1, $implementers);

		$implementer = $implementers[0];
		$this->assertInstanceOf(ClassReflectionInterface::class, $implementer);
		$this->assertSame(ChildInterface::class, $implementer->getName());
	}


	public function testGetIndirectImplementersOfInterface()
	{
		$implementers = $this->parserStorage->getIndirectImplementersOfInterface($this->parentInterfaceReflection);
		$this->assertCount(1, $implementers);

		$implementer = $implementers[0];
		$this->assertInstanceOf(ClassReflectionInterface::class, $implementer);
		$this->assertSame(SomeClass::class, $implementer->getName());
	}

}

<?php

namespace ApiGen\Tests\Parser;

use ApiGen\Configuration\Configuration;
use ApiGen\Parser\Parser;
use ApiGen\Parser\ParserResult;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Tests\ContainerAwareTestCase;
use ApiGen\Tests\Parser\ParserResultImplementers\ChildInterface;
use ApiGen\Tests\Parser\ParserResultImplementers\ParentInterface;
use ApiGen\Tests\Parser\ParserResultImplementers\SomeClass;
use Nette\Utils\Finder;


class ParserResultImplementersTest extends ContainerAwareTestCase
{

	/**
	 * @var ParserResult
	 */
	private $parserResult;

	/**
	 * @var ReflectionClass
	 */
	private $parentInterfaceReflection;


	protected function setUp()
	{
		$finder = Finder::find('*')->in(__DIR__ . '/ParserResultImplementers');
		$files = iterator_to_array($finder->getIterator());

		/** @var Configuration $configuration */
		$configuration = $this->container->getByType(Configuration::class);
		$configuration->resolveOptions([
			'source' => 'src',
			'destination' => TEMP_DIR . '/api'
		]);

		/** @var Parser $parser */
		$parser = $this->container->getByType(Parser::class);
		$parser->parse($files);

		$this->parserResult = $this->container->getByType(ParserResult::class);
		$classes = $this->parserResult->getClasses();
		$this->parentInterfaceReflection = $classes[ParentInterface::class];
	}


	public function testGetDirectImplementersOfInterface()
	{
		$implementers = $this->parserResult->getDirectImplementersOfInterface($this->parentInterfaceReflection);
		$this->assertCount(1, $implementers);

		$implementer = $implementers[0];
		$this->assertInstanceOf(ReflectionClass::class, $implementer);
		$this->assertSame(ChildInterface::class, $implementer->getName());
	}


	public function testGetIndirectImplementersOfInterface()
	{
		$implementers = $this->parserResult->getIndirectImplementersOfInterface($this->parentInterfaceReflection);
		$this->assertCount(1, $implementers);

		$implementer = $implementers[0];
		$this->assertInstanceOf(ReflectionClass::class, $implementer);
		$this->assertSame(SomeClass::class, $implementer->getName());
	}

}

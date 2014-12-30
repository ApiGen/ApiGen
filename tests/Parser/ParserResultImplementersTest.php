<?php

namespace ApiGen\Tests\Parser;

use ApiGen\Configuration\Configuration;
use ApiGen\Parser\Parser;
use ApiGen\Parser\ParserResult;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Tests\ContainerAwareTestCase;
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
		$configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
		$configuration->resolveOptions([
			'source' => 'src',
			'destination' => TEMP_DIR . '/api'
		]);

		/** @var Parser $parser */
		$parser = $this->container->getByType('ApiGen\Parser\Parser');
		$parser->parse($files);

		$this->parserResult = $this->container->getByType('ApiGen\Parser\ParserResult');
		$classes = $this->parserResult->getClasses();
		$this->parentInterfaceReflection = $classes['ApiGen\Tests\Parser\ParserResultImplementers\ParentInterface'];
	}


	public function testGetDirectImplementersOfInterface()
	{
		$implementers = $this->parserResult->getDirectImplementersOfInterface($this->parentInterfaceReflection);
		$this->assertCount(1, $implementers);

		$implementer = $implementers[0];
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionClass', $implementer);
		$this->assertSame('ApiGen\Tests\Parser\ParserResultImplementers\ChildInterface', $implementer->getName());
	}


	public function testGetIndirectImplementersOfInterface()
	{
		$implementers = $this->parserResult->getIndirectImplementersOfInterface($this->parentInterfaceReflection);
		$this->assertCount(1, $implementers);

		$implementer = $implementers[0];
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionClass', $implementer);
		$this->assertSame('ApiGen\Tests\Parser\ParserResultImplementers\SomeClass', $implementer->getName());
	}

}

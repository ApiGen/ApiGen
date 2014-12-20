<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\Configuration;
use ApiGen\Parser\Parser;
use ApiGen\Parser\ParserResult;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Tests\ContainerAwareTestCase;
use Nette\Utils\Finder;


class ReflectionMethodTest extends ContainerAwareTestCase
{

	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @var ParserResult
	 */
	private $parserResult;

	/**
	 * @var Configuration
	 */
	private $configuration;


	protected function setUp()
	{
		$this->parser = $this->container->getByType('ApiGen\Parser\Parser');
		$this->parserResult = $this->container->getByType('ApiGen\Parser\ParserResult');
		$this->configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
	}


	public function testMethodDefaultParameters()
	{
		$this->setConfiguration();
		$reflectionMethod = $this->getReflectionMethod();
		$this->assertCount(3, $reflectionMethod->getParameters());
	}


	private function setConfiguration()
	{
		$this->configuration->resolveOptions([
			'destination' => TEMP_DIR . '/api',
			'source' => '...'
		]);
	}


	/**
	 * @return ReflectionMethod
	 */
	private function getReflectionMethod()
	{
		$files = Finder::findFiles('*')->in(__DIR__ . '/ReflectionMethodSource')->getIterator();

		$this->parser->parse($files);
		/** @var ReflectionClass $reflectionClass */
		$reflectionClass = $this->parserResult->getClasses()['Project\ReflectionMethod'];
		$reflectionMethod = $reflectionClass->getMethod('methodWithArgs');
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionMethod', $reflectionMethod);
		return $reflectionMethod;
	}

}

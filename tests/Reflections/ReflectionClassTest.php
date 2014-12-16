<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\Configuration;
use ApiGen\Parser\Parser;
use ApiGen\Parser\ParserResult;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Tests\ContainerAwareTestCase;
use Nette\Utils\Finder;


class ReflectionClassTest extends ContainerAwareTestCase
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


	public function testPublic()
	{
		$this->setConfigurationAccessLevelOption(['public']);
		$accessLevelsClassReflection = $this->getAccessLevelsClassReflection();

		$this->assertTrue($accessLevelsClassReflection->hasMethod('publicMethod'));
		$this->assertFalse($accessLevelsClassReflection->hasMethod('protectedMethod'));
		$this->assertFalse($accessLevelsClassReflection->hasMethod('privateMethod'));
	}


	public function testProtectedPrivate()
	{
		$this->setConfigurationAccessLevelOption(['protected', 'private']);
		$accessLevelsClassReflection = $this->getAccessLevelsClassReflection();

		$this->assertFalse($accessLevelsClassReflection->hasMethod('publicMethod'));
		$this->assertTrue($accessLevelsClassReflection->hasMethod('protectedMethod'));
		$this->assertTrue($accessLevelsClassReflection->hasMethod('privateMethod'));
	}


	private function setConfigurationAccessLevelOption(array $values)
	{
		$this->configuration->resolveOptions([
			'destination' => '...',
			'source' => '...',
			'accessLevels' => $values
		]);
	}


	/**
	 * @return ReflectionClass $accessLevelsClassReflection
	 */
	private function getAccessLevelsClassReflection()
	{
		$files = Finder::findFiles('*')->in(__DIR__ . '/ReflectionClassSource')->getIterator();

		$this->parser->parse($files);
		$classes = $this->parserResult->getClasses();
		$classReflection = $classes['Project\AccessLevels'];
		$this->assertInstanceOf('ApiGen\Reflection\ReflectionClass', $classReflection);
		return $classReflection;
	}

}

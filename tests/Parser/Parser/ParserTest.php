<?php

namespace ApiGen\Parser\Tests;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use Nette\Utils\Finder;
use ReflectionProperty;


class ParserTest extends ContainerAwareTestCase
{

	/**
	 * @var ParserInterface
	 */
	private $parser;

	/**
	 * @var ParserStorageInterface
	 */
	private $parserResult;

	/**
	 * @var ConfigurationInterface
	 */
	private $configuration;


	protected function setUp()
	{
		$this->parser = $this->container->getByType(ParserInterface::class);
		$this->parserResult = $this->container->getByType(ParserStorageInterface::class);
		$this->configuration = $this->container->getByType(ConfigurationInterface::class);
		/** @var ConfigurationInterface $configuration */
		$configuration = $this->container->getByType(ConfigurationInterface::class);
		$configuration->setOptions(['visibilityLevels' => ReflectionProperty::IS_PUBLIC]);
	}


	public function testParseClasses()
	{
		$this->assertCount(0, $this->parserResult->getClasses());

		$this->parser->parse($this->getFilesFromDir(__DIR__ . '/ParserSource'));
		$this->assertCount(4, $this->parserResult->getClasses());

		$this->assertCount(1, $this->parser->getErrors());
	}


	/**
	 * @param string $dir
	 * @return array { filePath => size }
	 */
	private function getFilesFromDir($dir)
	{
		$files = [];
		foreach (Finder::find('*.php')->in($dir) as $splFile) {
			$files[] = $splFile;
		}
		return $files;
	}

}

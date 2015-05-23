<?php

namespace ApiGen\Parser\Tests;

use ApiGen\Contracts\Parser\Configuration\ParserConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Parser\Parser;
use Nette\Utils\Finder;


class ParserTest extends ContainerAwareTestCase
{

	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @var ParserStorageInterface
	 */
	private $parserResult;

	/**
	 * @var ParserConfigurationInterface
	 */
	private $configuration;


	protected function setUp()
	{
		$this->parser = $this->container->getByType('ApiGen\Contracts\Parser\ParserInterface');
		$this->parserResult = $this->container->getByType('ApiGen\Contracts\Parser\ParserStorageInterface');
		$this->configuration = $this->container->getByType(
			'ApiGen\Contracts\Parser\Configuration\ParserConfigurationInterface'
		);
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

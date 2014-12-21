<?php

namespace ApiGen\Tests\Parser;

use ApiGen\Configuration\Configuration;
use ApiGen\Parser\Parser;
use ApiGen\Parser\ParserResult;
use ApiGen\Tests\ContainerAwareTestCase;
use Nette\Utils\Finder;


class ParserTest extends ContainerAwareTestCase
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
		$this->setupConfigDefaults(); // required by Broker
	}


	public function testParseClasses()
	{
		$this->assertCount(0, $this->parserResult->getClasses());

		$this->parser->parse($this->getFilesFromDir(__DIR__ . '/Source'));
		$this->assertCount(3, $this->parserResult->getClasses());
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


	private function setupConfigDefaults()
	{
		$defaults['source'] = TEMP_DIR;
		$defaults['destination'] = TEMP_DIR . '/api';
		$this->configuration->resolveOptions($defaults);
	}

}

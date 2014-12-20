<?php

namespace ApiGen\Tests\ApiGen\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\TreeGenerator;
use ApiGen\Generator\TemplateGenerators\ZipGenerator;
use ApiGen\Parser\Parser;
use ApiGen\Tests\ContainerAwareTestCase;
use Nette\Utils\Finder;


class TreeGeneratorTest extends ContainerAwareTestCase
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @var TreeGenerator
	 */
	private $treeGenerator;


	protected function setUp()
	{
		$this->configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
		$this->parser = $this->container->getByType('ApiGen\Parser\Parser');
		$this->treeGenerator = $this->container->getByType('ApiGen\Generator\TemplateGenerators\TreeGenerator');
	}


	public function testIsAllowed()
	{
		$this->configuration->resolveOptions([
			'source' => '...',
			'destination' => TEMP_DIR . '/api'
		]);
		$this->assertTrue($this->treeGenerator->isAllowed());
		$this->configuration->resolveOptions([
			'source' => '...',
			'destination' => TEMP_DIR . '/api',
			'tree' => FALSE
		]);
		$this->assertFalse($this->treeGenerator->isAllowed());
	}


	public function testGenerate()
	{
		$this->prepareTreeGeneratorRequirements();
		$this->treeGenerator->generate();
		$this->assertFileExists($this->destinationDir . '/tree.html');
	}


	private function prepareTreeGeneratorRequirements()
	{
		$this->configuration->resolveOptions([
			'source' => '...',
			'destination' => $this->destinationDir
		]);

		$files = [];
		foreach (Finder::findFiles('*')->in(__DIR__ . '/TodoSources')->getIterator() as $file) {
			$files[] = $file;
		}
		$this->parser->parse($files);
	}

}

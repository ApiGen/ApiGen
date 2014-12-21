<?php

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\OpensearchGenerator;
use ApiGen\Generator\TemplateGenerators\OverviewGenerator;
use ApiGen\Tests\ContainerAwareTestCase;


class OpensearchGeneratorTest extends ContainerAwareTestCase
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var OpensearchGenerator
	 */
	private $opensearchGenerator;


	protected function setUp()
	{
		$this->configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
		$this->opensearchGenerator = $this->container->getByType('ApiGen\Generator\TemplateGenerators\OpensearchGenerator');
	}


	public function testIsAllowed()
	{
		$this->configuration->resolveOptions([
			'destination' => TEMP_DIR . '/api',
			'source' => TEMP_DIR,
			'googleCseId' => NULL,
			'baseUrl' => NULL
		]);
		$this->assertFalse($this->opensearchGenerator->isAllowed());
		$this->setCorrectConfiguration();
		$this->assertTrue($this->opensearchGenerator->isAllowed());
	}


	public function testGenerate()
	{
		$this->setCorrectConfiguration();
		$this->opensearchGenerator->generate();
		$this->assertFileExists(TEMP_DIR . '/api/opensearch.xml');
	}


	private function setCorrectConfiguration()
	{
		$this->configuration->resolveOptions([
			'destination' => TEMP_DIR . '/api',
			'source' => TEMP_DIR,
			'googleCseId' => '12345',
			'baseUrl' => 'http://apigen.org'
		]);
	}

}

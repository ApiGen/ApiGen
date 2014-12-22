<?php

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\CombinedGenerator;
use ApiGen\Tests\ContainerAwareTestCase;


class CombinedGeneratorTest extends ContainerAwareTestCase
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var CombinedGenerator
	 */
	private $combinedGenerator;


	protected function setUp()
	{
		$this->configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
		$this->combinedGenerator = $this->container->getByType('ApiGen\Generator\TemplateGenerators\CombinedGenerator');
	}


	public function testGenerate()
	{
		$this->configuration->resolveOptions([
			'source' => TEMP_DIR,
			'destination' => TEMP_DIR . '/api'
		]);
		$this->combinedGenerator->generate();
		$this->assertFileExists(TEMP_DIR . '/api/resources/combined.js');
	}

}

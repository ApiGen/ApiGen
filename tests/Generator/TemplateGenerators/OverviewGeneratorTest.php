<?php

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\OverviewGenerator;
use ApiGen\Tests\ContainerAwareTestCase;


class OverviewGeneratorTest extends ContainerAwareTestCase
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var OverviewGenerator
	 */
	private $overviewGenerator;


	protected function setUp()
	{
		$this->configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
		$this->overviewGenerator = $this->container->getByType('ApiGen\Generator\TemplateGenerators\OverviewGenerator');
	}


	public function testGenerate()
	{
		$this->configuration->resolveOptions([
			'destination' => TEMP_DIR . '/api',
			'source' => TEMP_DIR
		]);
		$this->overviewGenerator->generate();
		$this->assertFileExists(TEMP_DIR . '/api/index.html');
	}

}

<?php

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\E404Generator;
use ApiGen\Tests\ContainerAwareTestCase;


class E404GeneratorTest extends ContainerAwareTestCase
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var E404Generator
	 */
	private $e404Generator;


	protected function setUp()
	{
		$this->configuration = $this->container->getByType(Configuration::class);
		$this->e404Generator = $this->container->getByType(E404Generator::class);
	}


	public function testGenerate()
	{
		$this->configuration->resolveOptions([
			'destination' => TEMP_DIR . '/api',
			'source' => TEMP_DIR
		]);
		$this->e404Generator->generate();
		$this->assertFileExists(TEMP_DIR . '/api/404.html');
	}

}

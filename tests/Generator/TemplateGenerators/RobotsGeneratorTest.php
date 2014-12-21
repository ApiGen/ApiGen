<?php

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\RobotsGenerator;
use ApiGen\Tests\ContainerAwareTestCase;


class RobotsGeneratorTest extends ContainerAwareTestCase
{

	const BASE_URL = 'http://apigen.org';

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var RobotsGenerator
	 */
	private $robotsGenerator;


	protected function setUp()
	{
		$this->configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
		$this->robotsGenerator = $this->container->getByType('ApiGen\Generator\TemplateGenerators\RobotsGenerator');
	}


	public function testIsAllowed()
	{
		$this->assertFalse($this->robotsGenerator->isAllowed());
		$this->setCorrectConfiguration();
		$this->assertTrue($this->robotsGenerator->isAllowed());
	}


	public function testGenerate()
	{
		$this->setCorrectConfiguration();
		$this->robotsGenerator->generate();
		$this->assertFileExists(TEMP_DIR . '/api/robots.txt');
	}


	private function setCorrectConfiguration()
	{
		$this->configuration->resolveOptions([
			'destination' => TEMP_DIR . '/api',
			'source' => TEMP_DIR,
			'baseUrl' => self::BASE_URL
		]);
	}

}

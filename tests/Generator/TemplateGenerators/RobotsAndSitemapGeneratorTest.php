<?php

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\RobotsGenerator;
use ApiGen\Generator\TemplateGenerators\SitemapGenerator;
use ApiGen\Tests\ContainerAwareTestCase;


class RobotsAndSitemapGeneratorTest extends ContainerAwareTestCase
{

	const BASE_URL = 'http://apigen.org';

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var SitemapGenerator
	 */
	private $sitemapGenerator;

	/**
	 * @var RobotsGenerator
	 */
	private $robotsGenerator;


	protected function setUp()
	{
		$this->configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
		$this->sitemapGenerator = $this->container->getByType('ApiGen\Generator\TemplateGenerators\SitemapGenerator');
		$this->robotsGenerator = $this->container->getByType('ApiGen\Generator\TemplateGenerators\RobotsGenerator');
	}


	public function testBaseUrl()
	{
		$options = $this->configuration->resolveOptions([
			'destination' => TEMP_DIR . '/api',
			'source' => TEMP_DIR,
			'baseUrl' => self::BASE_URL
		]);
		$this->assertSame(self::BASE_URL, $options['baseUrl']);

		$this->assertTrue($this->sitemapGenerator->isAllowed());
		$this->assertTrue($this->robotsGenerator->isAllowed());
	}

}

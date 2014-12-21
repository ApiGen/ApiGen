<?php

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\SitemapGenerator;
use ApiGen\Tests\ContainerAwareTestCase;


class SitemapGeneratorTest extends ContainerAwareTestCase
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var SitemapGenerator
	 */
	private $sitemapGenerator;


	protected function setUp()
	{
		$this->configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
		$this->sitemapGenerator = $this->container->getByType('ApiGen\Generator\TemplateGenerators\SitemapGenerator');
	}


	public function testIsAllowed()
	{
		$this->assertFalse($this->sitemapGenerator->isAllowed());

		$this->configuration->resolveOptions([
			'destination' => TEMP_DIR . '/api',
			'source' => TEMP_DIR,
			'baseUrl' => 'http://site.com'
		]);
		$this->assertTrue($this->sitemapGenerator->isAllowed());
	}


	public function testGenerate()
	{
		$this->configuration->resolveOptions([
			'destination' => TEMP_DIR . '/api',
			'source' => TEMP_DIR,
			'baseUrl' => 'http://site.com'
		]);
		$this->sitemapGenerator->generate();
		$this->assertFileExists(TEMP_DIR . '/api/sitemap.xml');
	}

}

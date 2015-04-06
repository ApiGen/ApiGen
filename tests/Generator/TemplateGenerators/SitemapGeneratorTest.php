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
		$this->configuration = $this->container->getByType(Configuration::class);
		$this->sitemapGenerator = $this->container->getByType(SitemapGenerator::class);
	}


	public function testIsAllowed()
	{
		$this->assertFalse($this->sitemapGenerator->isAllowed());
		$this->setCorrectConfiguration();
		$this->assertTrue($this->sitemapGenerator->isAllowed());
	}


	public function testGenerate()
	{
		$this->setCorrectConfiguration();
		$this->sitemapGenerator->generate();
		$this->assertFileExists(TEMP_DIR . '/api/sitemap.xml');
	}


	private function setCorrectConfiguration()
	{
		$this->configuration->resolveOptions([
			'destination' => TEMP_DIR . '/api',
			'source' => TEMP_DIR,
			'baseUrl' => 'http://site.com'
		]);
	}

}

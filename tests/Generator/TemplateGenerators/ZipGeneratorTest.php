<?php

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\DeprecatedGenerator;
use ApiGen\Generator\TemplateGenerators\ZipGenerator;
use ApiGen\Parser\Parser;
use ApiGen\Templating\Template;
use ApiGen\Tests\ContainerAwareTestCase;
use Latte\Engine;
use Nette\Utils\Finder;
use ReflectionClass;


class ZipGeneratorTest extends ContainerAwareTestCase
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
	 * @var ZipGenerator
	 */
	private $zipGenerator;


	protected function setUp()
	{
		$this->configuration = $this->container->getByType(Configuration::class);
		$this->parser = $this->container->getByType(Parser::class);
		$this->zipGenerator = $this->container->getByType(ZipGenerator::class);
	}


	public function testIsAllowed()
	{
		$this->configuration->resolveOptions([
			'source' => __DIR__ . '/Source',
			'destination' => TEMP_DIR . '/api'
		]);
		$this->assertFalse($this->zipGenerator->isAllowed());
		$this->configuration->resolveOptions([
			'source' => __DIR__ . '/Source',
			'destination' => TEMP_DIR . '/api',
			'download' => TRUE
		]);
		$this->assertTrue($this->zipGenerator->isAllowed());
	}


	public function testGenerate()
	{
		if ( ! file_exists($this->destinationDir)) {
			mkdir($this->destinationDir);
		}

		$this->configuration->resolveOptions([
			'source' => __DIR__ . '/ZipGeneratorSource',
			'destination' => $this->destinationDir,
			'download' => TRUE
		]);

		$this->zipGenerator->generate();
		$this->assertFileExists($this->destinationDir . '/API-documentation.zip');
	}

}

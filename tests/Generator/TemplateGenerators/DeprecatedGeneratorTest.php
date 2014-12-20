<?php

namespace ApiGen\Tests\ApiGen\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\DeprecatedGenerator;
use ApiGen\Parser\Parser;
use ApiGen\Templating\Template;
use ApiGen\Tests\ContainerAwareTestCase;
use Latte\Engine;
use Nette\Utils\Finder;
use ReflectionClass;


class DeprecatedGeneratorTest extends ContainerAwareTestCase
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
	 * @var DeprecatedGenerator
	 */
	private $deprecatedGenerator;


	protected function setUp()
	{
		$this->configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
		$this->parser = $this->container->getByType('ApiGen\Parser\Parser');
		$this->deprecatedGenerator = $this->container->getByType('ApiGen\Generator\TemplateGenerators\DeprecatedGenerator');
	}


	public function testIsAllowed()
	{
		$this->configuration->resolveOptions([
			'source' => '...',
			'destination' => TEMP_DIR . '/api'
		]);
		$this->assertFalse($this->deprecatedGenerator->isAllowed());
		$this->configuration->resolveOptions([
			'source' => '...',
			'destination' => TEMP_DIR . '/api',
			'deprecated' => TRUE
		]);
		$this->assertTrue($this->deprecatedGenerator->isAllowed());
	}


	public function testSetDeprecatedElementsToTemplate()
	{
		$this->prepareDeprecatedGeneratorRequirements();
		$template = $this->runSetDeprecatedElementsToTemplate(new Template(new Engine));

		/** @var Template $template */
		$parameters = $template->getParameters();
		$this->assertCount(1, $parameters['deprecatedClasses']);
		$this->assertCount(1, $parameters['deprecatedMethods']);
	}


	private function prepareDeprecatedGeneratorRequirements()
	{
		$this->configuration->resolveOptions([
			'source' => '...',
			'destination' => TEMP_DIR . '/api',
			'deprecated' => TRUE
		]);

		$files = [];
		foreach (Finder::findFiles('*')->in(__DIR__ . '/DeprecatedSources')->getIterator() as $file) {
			$files[] = $file;
		}
		$this->parser->parse($files);
	}


	/**
	 * @param Template $template
	 * @return Template
	 */
	private function runSetDeprecatedElementsToTemplate(Template $template)
	{
		$classReflection = new ReflectionClass($this->deprecatedGenerator);
		$methodReflection = $classReflection->getMethod('setDeprecatedElementsToTemplate');
		$methodReflection->setAccessible(TRUE);
		return $methodReflection->invokeArgs($this->deprecatedGenerator, [$template]);
	}

}

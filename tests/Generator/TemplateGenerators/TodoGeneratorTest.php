<?php

namespace ApiGen\Tests\ApiGen\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\TodoGenerator;
use ApiGen\Parser\Parser;
use ApiGen\Templating\Template;
use ApiGen\Tests\ContainerAwareTestCase;
use Latte\Engine;
use Nette\Utils\Finder;
use ReflectionClass;


class TodoGeneratorTest extends ContainerAwareTestCase
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
	 * @var TodoGenerator
	 */
	private $todoGenerator;


	protected function setUp()
	{
		$this->configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
		$this->parser = $this->container->getByType('ApiGen\Parser\Parser');
		$this->todoGenerator = $this->container->getByType('ApiGen\Generator\TemplateGenerators\TodoGenerator');
	}


	public function testIsAllowed()
	{
		$this->configuration->resolveOptions([
			'source' => '...',
			'destination' => '...'
		]);
		$this->assertFalse($this->todoGenerator->isAllowed());
		$this->configuration->resolveOptions([
			'source' => '...',
			'destination' => '...',
			'todo' => TRUE
		]);
		$this->assertTrue($this->todoGenerator->isAllowed());
	}


	public function testSetTodoElementsToTemplate()
	{
		$this->prepareTodoGeneratorRequirements();
		$template = $this->runSetTodoElementsToTemplate(new Template(new Engine));

		/** @var Template $template */
		$parameters = $template->getParameters();
		$this->assertCount(1, $parameters['todoClasses']);
		$this->assertCount(1, $parameters['todoMethods']);
	}


	private function prepareTodoGeneratorRequirements()
	{
		$this->configuration->resolveOptions([
			'source' => __DIR__ . '/TodoSources',
			'destination' => '...',
			'todo' => TRUE
		]);

		$files = [];
		foreach (Finder::findFiles('*')->in(__DIR__ . '/TodoSources')->getIterator() as $file) {
			$files[] = $file;
		}
		$this->parser->parse($files);
	}


	/**
	 * @param Template $template
	 * @return Template
	 */
	private function runSetTodoElementsToTemplate(Template $template)
	{
		$classReflection = new ReflectionClass($this->todoGenerator);
		$methodReflection = $classReflection->getMethod('setTodoElementsToTemplate');
		$methodReflection->setAccessible(TRUE);
		return $methodReflection->invokeArgs($this->todoGenerator, [$template]);
	}

}

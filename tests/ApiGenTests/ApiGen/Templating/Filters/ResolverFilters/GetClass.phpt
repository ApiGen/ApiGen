<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Templating\Filters\ResolverFilters;

use ApiGen\Neon\NeonFile;
use ApiGen\PharCompiler;
use ApiGen\Templating\TemplateFactory;
use ApiGenTests\TestCase;
use Latte;
use Nette\Configurator;
use Tester\Assert;


require_once __DIR__ . '/../../../../bootstrap.php';


class FilterTests extends TestCase
{

	public function testAvailability()
	{
		$container = $this->createContainer();
		Assert::type('Nette\DI\Container', $container);

		/** @var Latte\Engine $latte */
		$latte = $container->getByType('Latte\Engine');
		$result = $latte->invokeFilter('getClass', ['foo']);
		Assert::false($result);
	}


	/**
	 * @return \Nette\DI\Container|\SystemContainer
	 */
	private function createContainer()
	{
		$configurator = new Configurator;
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig(__DIR__ . '/../../../../../../src/ApiGen/DI/config.neon');
		return $configurator->createContainer();
	}

}


(new FilterTests)->run();

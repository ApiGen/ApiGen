<?php

namespace ApiGen\Utils\Tests\DI;

use ApiGen\Utils\DI\UtilsExtension;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;
use PHPUnit_Framework_TestCase;


class UtilsExtensionTest extends PHPUnit_Framework_TestCase
{

	public function testLoadConfiguration()
	{
		$utilsExtension = new UtilsExtension;
		$utilsExtension->setCompiler(new Compiler(new ContainerBuilder), 'compiler');
		$utilsExtension->loadConfiguration();

		$builder = $utilsExtension->getContainerBuilder();
		$builder->prepareClassList();

		$found = $builder->findByType('ApiGen\Utils\FileSystem');
		/** @var ServiceDefinition $fileSystemDefinition */
		$fileSystemDefinition = array_pop($found);
		$this->assertSame('ApiGen\Utils\FileSystem', $fileSystemDefinition->getClass());
	}

}

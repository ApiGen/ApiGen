<?php

namespace ApiGen\Tests;

use ApiGen\FileSystem\FileSystem;
use Nette\Configurator;
use Nette\DI\Container;


class ContainerFactory
{

	/**
	 * @return Container
	 */
	public function create()
	{
		$configurator = new Configurator;
		$configurator->setTempDirectory($this->createAndReturnTempDir());
		$configurator->addConfig(__DIR__ . '/config/default.neon');
		return $configurator->createContainer();
	}


	/**
	 * @return string
	 */
	private function createAndReturnTempDir()
	{
		@mkdir(__DIR__ . '/tmp'); // @ - directory may exists
		@mkdir($tempDir = __DIR__ . '/tmp/' . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
		FileSystem::purgeDir($tempDir);
		return realpath($tempDir);
	}

}

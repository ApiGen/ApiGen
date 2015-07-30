<?php

namespace ApiGen\EventDispatcher\Tests;

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
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->addConfig(__DIR__ . '/config/default.neon');
        return $configurator->createContainer();
    }
}

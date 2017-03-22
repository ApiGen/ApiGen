<?php declare(strict_types=1);

namespace ApiGen\Tests;

use Nette\Configurator;
use Nette\DI\Container;

class ContainerFactory
{

    public function create(): Container
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->addConfig(__DIR__ . '/config/default.neon');
        $configurator->addParameters(['rootDir' => __DIR__ . '/..']);
        return $configurator->createContainer();
    }
}

<?php declare(strict_types=1);

namespace ApiGen\Tests;

use Nette\Configurator;
use Nette\DI\Container;

final class ContainerFactory
{
    public function create(): Container
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->addConfig(__DIR__ . '/../src/config/config.neon');
        return $configurator->createContainer();
    }
}

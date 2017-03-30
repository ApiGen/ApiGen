<?php declare(strict_types=1);

namespace ApiGen\DI\Container;

use Nette\Configurator;
use Nette\DI\Container;

final class ContainerFactory
{
    public function create(): Container
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory(sys_get_temp_dir() . '/_apigen');
        $configurator->addConfig(__DIR__ . '/../../config/config.neon');
        return $configurator->createContainer();
    }
}

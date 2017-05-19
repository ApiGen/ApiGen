<?php declare(strict_types=1);

namespace ApiGen\StringRouting\DI;

use ApiGen\StringRouting\Contract\Route\RouteInterface;
use ApiGen\StringRouting\StringRouter;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class StringRoutingExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );
    }

    public function beforeCompile(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            StringRouter::class,
            RouteInterface::class,
            'addRoute'
        );
    }
}

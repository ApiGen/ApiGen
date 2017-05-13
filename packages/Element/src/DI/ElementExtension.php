<?php declare(strict_types=1);

namespace ApiGen\Element\DI;

use ApiGen\Element\Contract\ReflectionCollector\ReflectionCollectorCollectorInterface;
use ApiGen\Element\Contract\ReflectionCollector\ReflectionCollectorInterface;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class ElementExtension extends CompilerExtension
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
            ReflectionCollectorCollectorInterface::class,
            ReflectionCollectorInterface::class,
            'addReflectionCollector'
        );
    }
}

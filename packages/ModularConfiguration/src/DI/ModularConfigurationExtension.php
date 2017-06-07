<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\DI;

use ApiGen\ModularConfiguration\ConfigurationResolver;
use ApiGen\ModularConfiguration\Contract\CommandDecoratorInterface;
use ApiGen\ModularConfiguration\Contract\Option\CommandBoundInterface;
use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class ModularConfigurationExtension extends CompilerExtension
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
            CommandDecoratorInterface::class,
            CommandBoundInterface::class,
            'addOption'
        );

        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            ConfigurationResolver::class,
            OptionInterface::class,
            'addOption'
        );
    }
}

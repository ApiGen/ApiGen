<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\DI;

use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;
use ApiGen\ModularConfiguration\Contract\ConfigurationDecoratorInterface;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class ModularConfigurationExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );
    }

    public function beforeCompile()
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            ConfigurationDecoratorInterface::class,
            OptionInterface::class,
            'addConfigurationOption'
        );
    }
}

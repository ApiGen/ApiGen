<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\DI;

use ApiGen\ModularConfiguration\Contract\ConfigurationOptionInterface;
use ApiGen\ModularConfiguration\Contract\ConfigurationResolverInterface;
use Nette\DI\CompilerExtension;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class ModularConfigurationExtension extends CompilerExtension
{
    public function beforeCompile()
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            ConfigurationResolverInterface::class,
            ConfigurationOptionInterface::class,
            'addConfigurationOption'
        );
    }
}

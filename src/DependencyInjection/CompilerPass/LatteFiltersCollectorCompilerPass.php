<?php declare(strict_types=1);

namespace ApiGen\DependencyInjection\CompilerPass;

use ApiGen\Contract\Templating\FilterProviderInterface;
use ApiGen\Latte\FiltersAwareLatteEngineFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionCollector;

final class LatteFiltersCollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            FiltersAwareLatteEngineFactory::class,
            FilterProviderInterface::class,
            'addFiltersProvider'
        );
    }
}

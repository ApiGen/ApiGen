<?php declare(strict_types=1);

namespace ApiGen\DependencyInjection\CompilerPass;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionCollector;

final class CollectCommandCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder)
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            Application::class,
            Command::class,
            'add'
        );
    }
}

<?php declare(strict_types=1);

namespace ApiGen\DependencyInjection\CompilerPass;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Annotation\Contract\AnnotationSubscriber\AnnotationSubscriberInterface;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Contract\Templating\FilterProviderInterface;
use ApiGen\Element\Contract\ReflectionCollector\BasicReflectionCollectorInterface;
use ApiGen\Element\ReflectionCollectorCollector;
use ApiGen\Generator\GeneratorQueue;
use ApiGen\Latte\FiltersAwareLatteEngineFactory;
use ApiGen\ModularConfiguration\CommandDecorator;
use ApiGen\ModularConfiguration\ConfigurationResolver;
use ApiGen\ModularConfiguration\Contract\Option\CommandBoundInterface;
use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\TransformerCollector;
use ApiGen\StringRouting\Contract\Route\RouteInterface;
use ApiGen\StringRouting\StringRouter;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionCollector;

final class CollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $this->collectCommandsToApplication($containerBuilder);
        $this->collectOptionsToCommandDecorator($containerBuilder);
        $this->collectOptionsToConfigurationResolver($containerBuilder);
        $this->collectTransformersToTransformerCollector($containerBuilder);
        $this->collectReflectionCollectorsToReflectionCollectorCollector($containerBuilder);
        $this->collectAnnotationSubscribersToAnnotationDecorator($containerBuilder);
        $this->collectRoutesToStringRouter($containerBuilder);
        $this->collectEventSubscribersToDispatcher($containerBuilder);
        $this->collectGeneratorsToGeneratorQueue($containerBuilder);
        $this->collectFilterProvidersToLatteEngine($containerBuilder);
    }

    private function collectCommandsToApplication(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            Application::class,
            Command::class,
            'add'
        );
    }

    private function collectOptionsToCommandDecorator(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            CommandDecorator::class,
            CommandBoundInterface::class,
            'addOption'
        );
    }

    private function collectOptionsToConfigurationResolver(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            ConfigurationResolver::class,
            OptionInterface::class,
            'addOption'
        );
    }

    private function collectTransformersToTransformerCollector(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            TransformerCollector::class,
            TransformerInterface::class,
            'addTransformer'
        );
    }

    private function collectReflectionCollectorsToReflectionCollectorCollector(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            ReflectionCollectorCollector::class,
            BasicReflectionCollectorInterface::class,
            'addReflectionCollector'
        );
    }

    private function collectAnnotationSubscribersToAnnotationDecorator(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            AnnotationDecorator::class,
            AnnotationSubscriberInterface::class,
            'addAnnotationSubscriber'
        );
    }

    private function collectRoutesToStringRouter(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            StringRouter::class,
            RouteInterface::class,
            'addRoute'
        );
    }

    private function collectEventSubscribersToDispatcher(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            EventDispatcher::class,
            EventSubscriberInterface::class,
            'addSubscriber'
        );
    }

    private function collectGeneratorsToGeneratorQueue(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            GeneratorQueue::class,
            GeneratorInterface::class,
            'addGenerator'
        );
    }

    private function collectFilterProvidersToLatteEngine(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            FiltersAwareLatteEngineFactory::class,
            FilterProviderInterface::class,
            'addFilterProvider'
        );
    }
}

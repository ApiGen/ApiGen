<?php declare(strict_types=1);

namespace ApiGen\DI;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Contracts\Annotation\AnnotationSubscriberInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use Latte\Engine;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class ApiGenExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );

        $this->setupTemplating();
    }

    public function beforeCompile(): void
    {
        $this->setupGeneratorQueue();
        $this->setupAnnotationDecorator();
    }

    private function setupTemplating(): void
    {
        // @todo: create and use Symplify package - FlatWhite
        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->addDefinition($this->prefix('latteFactory'))
            ->setClass(Engine::class)
            ->addSetup('setTempDirectory', [sys_get_temp_dir() . '/_latte_cache']);
    }

    private function setupGeneratorQueue(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            GeneratorQueueInterface::class,
            GeneratorInterface::class,
            'addGenerator'
        );
    }

    private function setupAnnotationDecorator(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            AnnotationDecorator::class,
            AnnotationSubscriberInterface::class,
            'addAnnotationSubscriber'
        );
    }
}

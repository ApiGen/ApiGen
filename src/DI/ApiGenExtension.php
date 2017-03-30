<?php declare(strict_types=1);

namespace ApiGen\DI;

use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Templating\Filters\Filters;
use Latte\Engine;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

final class ApiGenExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        $this->loadServicesFromConfig();
        $this->setupTemplating();
    }

    public function beforeCompile(): void
    {
        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->prepareClassList();
        $this->setupTemplatingFilters();
        $this->setupGeneratorQueue();
    }

    private function loadServicesFromConfig(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );
    }

    private function setupTemplating(): void
    {
        // @todo: create and use Symplify package - FlatWhite
        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->addDefinition($this->prefix('latteFactory'))
            ->setClass(Engine::class)
            ->addSetup('setTempDirectory', [$containerBuilder->expand('%tempDir%/cache/latte')]);
    }

    private function setupTemplatingFilters(): void
    {
        // @todo: use Symplify package
        $containerBuilder = $this->getContainerBuilder();
        $latteFactory = $containerBuilder->getDefinitionByType(Engine::class);
        foreach ($containerBuilder->findByType(Filters::class) as $definition) {
            $latteFactory->addSetup('addFilter', [null, ['@' . $definition->getClass(), 'loader']]);
        }
    }

    private function setupGeneratorQueue(): void
    {
        // @todo: use package builder for these collections
        $containerBuilder = $this->getContainerBuilder();
        $generator = $containerBuilder->getDefinitionByType(GeneratorQueueInterface::class);
        $services = $containerBuilder->findByType(TemplateGeneratorInterface::class);
        ksort($services, SORT_NATURAL);
        foreach ($services as $definition) {
            $generator->addSetup('addToQueue', ['@' . $definition->getClass()]);
        }
    }
}

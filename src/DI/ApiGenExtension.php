<?php declare(strict_types=1);

namespace ApiGen\DI;

use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Templating\Filters\Filters;
use Latte\Engine;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

class ApiGenExtension extends CompilerExtension
{

    public function loadConfiguration(): void
    {
        $this->loadServicesFromConfig();
        $this->setupTemplating();
    }


    public function beforeCompile(): void
    {
        $builder = $this->getContainerBuilder();
        $builder->prepareClassList();
        $this->setupTemplatingFilters();
        $this->setupGeneratorQueue();
    }


    private function loadServicesFromConfig(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/services.neon')['services']
        );
    }


    private function setupTemplating(): void
    {
        $builder = $this->getContainerBuilder();
        $builder->addDefinition($this->prefix('latteFactory'))
            ->setClass(Engine::class)
            ->addSetup('setTempDirectory', [$builder->expand('%tempDir%/cache/latte')]);
    }


    private function setupTemplatingFilters(): void
    {
        $builder = $this->getContainerBuilder();
        $latteFactory = $builder->getDefinition($builder->getByType(Engine::class));
        foreach ($builder->findByType(Filters::class) as $definition) {
            $latteFactory->addSetup('addFilter', [null, ['@' . $definition->getClass(), 'loader']]);
        }
    }


    private function setupGeneratorQueue(): void
    {
        $builder = $this->getContainerBuilder();
        $generator = $builder->getDefinition($builder->getByType(GeneratorQueueInterface::class));
        $services = $builder->findByType(TemplateGeneratorInterface::class);
        ksort($services, SORT_NATURAL);
        foreach ($services as $definition) {
            $generator->addSetup('addToQueue', ['@' . $definition->getClass()]);
        }
    }
}

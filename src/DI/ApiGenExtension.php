<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\DI;

use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Templating\Filters\Filters;
use Latte\Engine;
use Nette\DI\CompilerExtension;

class ApiGenExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $this->loadServicesFromConfig();
        $this->setupTemplating();
    }


    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();
        $builder->prepareClassList();
        $this->setupTemplatingFilters();
        $this->setupGeneratorQueue();
    }


    private function loadServicesFromConfig()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->loadFromFile(__DIR__ . '/services.neon');
        $this->compiler->parseServices($builder, $config);
    }


    private function setupTemplating()
    {
        $builder = $this->getContainerBuilder();
        $builder->addDefinition($this->prefix('latteFactory'))
            ->setClass(Engine::class)
            ->addSetup('setTempDirectory', [$builder->expand('%tempDir%/cache/latte')]);
    }


    private function setupTemplatingFilters()
    {
        $builder = $this->getContainerBuilder();
        $latteFactory = $builder->getDefinition($builder->getByType(Engine::class));
        foreach ($builder->findByType(Filters::class) as $definition) {
            $latteFactory->addSetup('addFilter', [null, ['@' . $definition->getClass(), 'loader']]);
        }
    }


    private function setupGeneratorQueue()
    {
        $builder = $this->getContainerBuilder();
        $generator = $builder->getDefinition($builder->getByType(GeneratorQueueInterface::class));
        foreach ($builder->findByType(TemplateGeneratorInterface::class) as $definition) {
            $generator->addSetup('addToQueue', ['@' . $definition->getClass()]);
        }
    }
}

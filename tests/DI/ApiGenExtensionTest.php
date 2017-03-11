<?php

namespace ApiGen\Tests\DI;

use ApiGen\Command\GenerateCommand;
use ApiGen\Console\Application;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\DI\ApiGenExtension;
use ApiGen\Generator\GeneratorQueue;
use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Generator\TemplateGenerators\ClassElementGenerator;
use ApiGen\Templating\Filters\AnnotationFilters;
use ApiGen\Tests\MethodInvoker;
use Latte\Engine;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;

class ApiGenExtensionTest extends TestCase
{

    /**
     * @return ApiGenExtension
     */
    public function testLoadServicesFromConfig()
    {
        $extension = $this->getExtension();
        MethodInvoker::callMethodOnObject($extension, 'loadServicesFromConfig');

        $builder = $extension->getContainerBuilder();
        $builder->prepareClassList();

        $definition = $builder->getDefinition($builder->getByType(GeneratorQueue::class));
        $this->assertSame(GeneratorQueue::class, $definition->getClass());

        $definition = $builder->getDefinition($builder->getByType(ElementResolver::class));
        $this->assertSame(ElementResolver::class, $definition->getClass());

        return $extension;
    }


    public function testSetupTemplating()
    {
        $extension = $this->getExtension();
        MethodInvoker::callMethodOnObject($extension, 'setupTemplating');

        $builder = $extension->getContainerBuilder();
        $builder->prepareClassList();

        $definition = $builder->getDefinition($builder->getByType(Engine::class));
        $this->assertSame(Engine::class, $definition->getClass());

        $this->assertSame(
            __DIR__ . '/../temp/cache/latte',
            $definition->getSetup()[0]->arguments[0]
        );
    }


    public function testSetupTemplatingFilters()
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();

        $builder = $extension->getContainerBuilder();
        $builder->prepareClassList();

        MethodInvoker::callMethodOnObject($extension, 'setupTemplatingFilters');

        $definition = $builder->getDefinition($builder->getByType(Engine::class));
        $this->assertSame(Engine::class, $definition->getClass());

        $filterService = $definition->getSetup()[1]->arguments[1][0];
        $command = $builder->getDefinition($builder->getServiceName($filterService));
        $this->assertSame(AnnotationFilters::class, $command->getClass());
    }


    public function testSetupGeneratorQueue()
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();

        $builder = $extension->getContainerBuilder();
        $builder->prepareClassList();

        MethodInvoker::callMethodOnObject($extension, 'setupGeneratorQueue');

        $definition = $builder->getDefinition($builder->getByType(GeneratorQueueInterface::class));
        $this->assertSame(GeneratorQueue::class, $definition->getClass());

        $filterService = $definition->getSetup()[8]->arguments[0];
        $command = $builder->getDefinition($builder->getServiceName($filterService));
        $this->assertSame(ClassElementGenerator::class, $command->getClass());
    }


    /**
     * @return ApiGenExtension
     */
    private function getExtension()
    {
        $extension = new ApiGenExtension;
        $extension->setCompiler($this->getCompiler(), 'compiler');
        return $extension;
    }


    /**
     * @return Compiler
     */
    private function getCompiler()
    {
        $compiler = new Compiler(new ContainerBuilder);
        $compiler->addConfig(['parameters' => [
            'rootDir' => __DIR__ . '/..',
            'tempDir' => __DIR__ . '/../temp'
        ]]);
        $compiler->compile();
        return $compiler;
    }
}

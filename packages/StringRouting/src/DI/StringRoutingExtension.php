<?php declare(strict_types=1);

namespace ApiGen\StringRouting\DI;

use ApiGen\StringRouting\Contract\Latte\LatteCompilerAwareInterface;
use ApiGen\StringRouting\Contract\Latte\Macro\LatteMacrosProviderInterface;
use ApiGen\StringRouting\Contract\Route\RouteInterface;
use ApiGen\StringRouting\StringRouter;
use Latte\Engine;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Symplify\ModularLatteFilters\Exception\DI\MissingLatteDefinitionException;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class StringRoutingExtension extends CompilerExtension
{
    /**
     * @var string
     */
    private const APPLICATION_LATTE_FACTORY_INTERFACE = ILatteFactory::class;

    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );
    }

    public function beforeCompile(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            StringRouter::class,
            RouteInterface::class,
            'addRoute'
        );

        $this->registerModularMacros();
    }

    private function registerModularMacros(): void
    {
        $containerBuilder = $this->getContainerBuilder();
        $latteDefinition = $this->getLatteDefinition();

        // there would be better DI, bug Latte\Compiler is created statically in Latte\Engine :(
        $latteCompilerAwareDefinitions = $containerBuilder->findByType(LatteCompilerAwareInterface::class);
        foreach ($latteCompilerAwareDefinitions as $latteCompilerAwareDefinition) {
            $latteCompilerAwareDefinition->addSetup(
                'setCompiler',
                ['@' . $latteDefinition->getClass(), 'getCompiler()']
            );
        }

        // register MacroSets services to Latte\Compiler
        $latteMacrosProviderDefinitions = $containerBuilder->findByType(LatteMacrosProviderInterface::class);
        foreach ($latteMacrosProviderDefinitions as $latteMacrosProviderDefinition) {
            $latteMacrosProviderDefinition->addSetup('install');
        }
    }

    private function getLatteDefinition(): ServiceDefinition
    {
        $containerBuilder = $this->getContainerBuilder();
        if ($containerBuilder->getByType(Engine::class)) {
            return $containerBuilder->getDefinitionByType(Engine::class);
        }

        if ($containerBuilder->getByType(self::APPLICATION_LATTE_FACTORY_INTERFACE)) {
            return $containerBuilder->getDefinitionByType(self::APPLICATION_LATTE_FACTORY_INTERFACE);
        }

        throw new MissingLatteDefinitionException(
            sprintf(
                'No services providing Latte\Engine was found. Register service either of %s or %s type.',
                Engine::class,
                ILatteFactory::class
            )
        );
    }
}

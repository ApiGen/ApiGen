<?php declare(strict_types=1);

namespace ApiGen\Reflection\DI;

use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\TransformerCollector;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class ReflectionExtension extends CompilerExtension
{
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
            TransformerCollector::class,
            TransformerInterface::class,
            'addTransformer'
        );
    }
}

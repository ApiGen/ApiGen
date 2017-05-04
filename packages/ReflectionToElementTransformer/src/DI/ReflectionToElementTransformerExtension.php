<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\DI;

use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class ReflectionToElementTransformerExtension extends CompilerExtension
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
            TransformerCollectorInterface::class,
            TransformerInterface::class,
            'addTransformer'
        );
    }
}

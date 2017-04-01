<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\DI;

use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

final class ReflectionToElementTransformerExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );
    }

    public function beforeCompile()
    {
        $containerBuilder = $this->getContainerBuilder();

        $transformerCollectorDefinition = $containerBuilder->getDefinitionByType(
            TransformerCollectorInterface::class
        );
        $transformerDefinitions = $containerBuilder->findByType(TransformerInterface::class);

        foreach ($transformerDefinitions as $name => $definition) {
            $transformerCollectorDefinition->addSetup('addTransformer', ['@' . $name]);
        }
    }
}

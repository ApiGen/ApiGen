<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Route;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\AbstractClassElementInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\AbstractInterfaceElementInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\AbstractTraitElementInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\StringRouting\Contract\Route\RouteInterface;

final class SourceCodeRoute implements RouteInterface
{
    /**
     * @var string
     */
    public const NAME = 'sourceCode';

    public function match(string $name): bool
    {
        return $name === self::NAME;
    }

    /**
     * @param AbstractReflectionInterface $reflection
     */
    public function constructUrl($reflection): string
    {
        if ($reflection instanceof ClassReflectionInterface) {
            return 'source-class-' . $reflection->getName() . '.html';
        }

        if ($reflection instanceof TraitReflectionInterface) {
            return 'source-trait-' . $reflection->getName() . '.html';
        }

        if ($reflection instanceof InterfaceReflectionInterface) {
            return 'source-interface-' . $reflection->getName() . '.html';
        }

        if ($reflection instanceof FunctionReflectionInterface) {
            return 'source-function-' . $reflection->getName() . '.html' . $this->buildLineAnchor($reflection);
        }

        if ($reflection instanceof StartAndEndLineInterface) {
            if ($reflection instanceof AbstractClassElementInterface) {
                return 'source-class-' . $reflection->getDeclaringClassName() . '.html'
                    . $this->buildLineAnchor($reflection);
            }

            if ($reflection instanceof AbstractInterfaceElementInterface) {
                return 'source-interface-' . $reflection->getDeclaringInterfaceName() . '.html'
                    . $this->buildLineAnchor($reflection);
            }

            if ($reflection instanceof AbstractTraitElementInterface) {
                return 'source-trait-' . $reflection->getDeclaringTraitName() . '.html'
                    . $this->buildLineAnchor($reflection);
            }
        }

        return '/';
    }

    private function buildLineAnchor(StartAndEndLineInterface $reflection): string
    {
        $anchor = '#' . $reflection->getStartLine();
        if ($reflection->getStartLine() !== $reflection->getEndLine()) {
            $anchor .= '-' . $reflection->getEndLine();
        }

        return $anchor;
    }
}

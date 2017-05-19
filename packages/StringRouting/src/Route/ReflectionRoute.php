<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Route;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\AbstractClassElementInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\AbstractInterfaceElementInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\AbstractTraitElementInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\StringRouting\Contract\Route\RouteInterface;

final class ReflectionRoute implements RouteInterface
{
    /**
     * @var string
     */
    public const NAME = 'reflection';

    public function match(string $name): bool
    {
        return $name === self::NAME;
    }

    /**
     * @param AbstractReflectionInterface $argument
     */
    public function constructUrl($reflection): string
    {
        if ($reflection instanceof ClassReflectionInterface) {
            return 'class-' . $reflection->getName() . '.html';
        }

        if ($reflection instanceof TraitReflectionInterface) {
            return 'trait-' . $reflection->getName() . '.html';
        }

        if ($reflection instanceof InterfaceReflectionInterface) {
            return 'interface-' . $reflection->getName() . '.html';
        }

        if ($reflection instanceof FunctionReflectionInterface) {
            return 'function-' . $reflection->getName() . '.html';
        }

        if ($reflection instanceof AbstractClassElementInterface) {
            $class = 'class-' . $reflection->getDeclaringClassName() . '.html';
            $anchorPrefix = '';
            if ($reflection instanceof ClassMethodReflectionInterface) {
                $anchorPrefix = '_';
            }

            if ($reflection instanceof ClassPropertyReflectionInterface) {
                $anchorPrefix = '$';
            }

            return $class . '#' . $anchorPrefix . $reflection->getName();
        }

        if ($reflection instanceof AbstractInterfaceElementInterface) {
            $interface = 'interface-' . $reflection->getDeclaringInterfaceName() . '.html';
            $anchorPrefix = '';
            if ($reflection instanceof InterfaceMethodReflectionInterface) {
                $anchorPrefix = '_';
            }

            return $interface . '#' . $anchorPrefix . $reflection->getName();
        }

        if ($reflection instanceof AbstractTraitElementInterface) {
            $trait = 'trait-' . $reflection->getDeclaringTraitName() . '.html';
            $anchorPrefix = '';
            if ($reflection instanceof TraitMethodReflectionInterface) {
                $anchorPrefix = '_';
            }

            if ($reflection instanceof TraitPropertyReflectionInterface) {
                $anchorPrefix = '$';
            }

            return $trait . '#' . $anchorPrefix . $reflection->getName();
        }

        return '/';
    }
}

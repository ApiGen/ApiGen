<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Route;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\AbstractClassElementInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\AbstractInterfaceElementInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\AbstractTraitElementInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\StringRouting\Contract\Route\RouteInterface;
use ApiGen\Utils\NamingHelper;

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
            return 'class-' . NamingHelper::nameToFilePath($reflection->getName(), null, false) . '.html';
        }

        if ($reflection instanceof TraitReflectionInterface) {
            return 'trait-' . NamingHelper::nameToFilePath($reflection->getName(), null, false) . '.html';
        }

        if ($reflection instanceof InterfaceReflectionInterface) {
            return 'interface-' . NamingHelper::nameToFilePath($reflection->getName(), null, false) . '.html';
        }

        if ($reflection instanceof FunctionReflectionInterface) {
            return 'function-' . NamingHelper::nameToFilePath($reflection->getName(), null, false) . '.html';
        }

        if ($reflection instanceof AbstractClassElementInterface) {
            $class = 'class-' . NamingHelper::nameToFilePath($reflection->getDeclaringClassName(), null, false) . '.html';
            $anchorPrefix = '';
            if ($reflection instanceof ClassMethodReflectionInterface) {
                $anchorPrefix = '_';
            }

            if ($reflection instanceof ClassPropertyReflectionInterface) {
                $anchorPrefix = '$';
            }

            return $class . '#' . $anchorPrefix . NamingHelper::nameToFilePath($reflection->getName(), null, false);
        }

        if ($reflection instanceof AbstractInterfaceElementInterface) {
            $interface = 'interface-'
                . NamingHelper::nameToFilePath($reflection->getDeclaringInterfaceName(), null, false) . '.html';
            $anchorPrefix = '';
            if ($reflection instanceof InterfaceMethodReflectionInterface) {
                $anchorPrefix = '_';
            }

            return $interface . '#' . $anchorPrefix . NamingHelper::nameToFilePath($reflection->getName(), null, false);
        }

        if ($reflection instanceof AbstractTraitElementInterface) {
            $trait = 'trait-' . NamingHelper::nameToFilePath($reflection->getDeclaringTraitName(), null, false) . '.html';
            $anchorPrefix = '';
            if ($reflection instanceof TraitMethodReflectionInterface) {
                $anchorPrefix = '_';
            }

            if ($reflection instanceof TraitPropertyReflectionInterface) {
                $anchorPrefix = '$';
            }

            return $trait . '#' . $anchorPrefix . NamingHelper::nameToFilePath($reflection->getName(), null, false);
        }

        return '/';
    }
}

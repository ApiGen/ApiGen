<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\InterfaceReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TraitReflectionInterface;

interface ParserInterface
{
    /**
     * @param string[] $directories
     */
    public function parseDirectories(array $directories): void;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(): array;

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(): array;

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(): array;

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(): array;

    // @legacy bellow @see \ApiGen\Parser\ParserStorage

    /**
     * @return ClassReflectionInterface[]|InterfaceReflectionInterface[]
     */
    public function getDirectImplementersOfInterface(InterfaceReflectionInterface $interfaceReflection): array;

    /**
     * @return ClassReflectionInterface[]|InterfaceReflectionInterface[]
     */
    public function getIndirectImplementersOfInterface(InterfaceReflectionInterface $interfaceReflection): array;
}

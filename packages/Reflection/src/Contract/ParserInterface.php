<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract;

use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\InterfaceReflectionInterface;

interface ParserInterface
{
    /**
     * @param string[] $directories
     */
    public function parseDirectories(array $directories): void;

    // @legacy bellow @see \ApiGen\Parser\ParserStorage

    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectImplementersOfInterface(InterfaceReflectionInterface $interfaceReflection): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectImplementersOfInterface(InterfaceReflectionInterface $interfaceReflection): array;
}

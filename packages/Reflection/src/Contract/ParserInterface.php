<?php declare(strict_types=1);

namespace ApiGen\Parser\Contract;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\InterfaceReflectionInterface;

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

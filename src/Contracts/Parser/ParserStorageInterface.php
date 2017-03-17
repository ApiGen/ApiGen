<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;

interface ParserStorageInterface
{

    public function getElementsByType(string $type): array;


    /**
     * Get stats for documented classes, functions and constants.
     */
    public function getDocumentedStats(): array;


    public function getClasses(): array;


    public function getConstants(): array;


    public function getFunctions(): array;


    /**
     * @return string[]
     */
    public function getTypes(): array;


    public function setClasses(array $classes): void;


    public function setConstants(array $constants): void;


    public function setFunctions(array $functions): void;


    public function setTokenizedClasses(array $tokenizedClasses): void;


    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectImplementersOfInterface(ClassReflectionInterface $reflectionClass): array;


    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectImplementersOfInterface(ClassReflectionInterface $reflectionClass): array;
}

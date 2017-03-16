<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ArrayObject;

interface ParserStorageInterface
{

    public function getElementsByType(string $type): ArrayObject;


    /**
     * Get stats for documented classes, functions and constants.
     */
    public function getDocumentedStats(): array;


    public function getClasses(): ArrayObject;


    public function getConstants(): ArrayObject;


    public function getFunctions(): ArrayObject;


    /**
     * @return string[]
     */
    public function getTypes(): array;


    public function setClasses(ArrayObject $classes): void;


    public function setConstants(ArrayObject $constants): void;


    public function setFunctions(ArrayObject $functions): void;


    public function setInternalClasses(ArrayObject $internalClasses): void;


    public function setTokenizedClasses(ArrayObject $tokenizedClasses): void;


    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectImplementersOfInterface(ClassReflectionInterface $reflectionClass): array;


    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectImplementersOfInterface(ClassReflectionInterface $reflectionClass): array;
}

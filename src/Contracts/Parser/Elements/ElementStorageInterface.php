<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;

interface ElementStorageInterface
{
    /**
     * @return mixed[]
     */
    public function getNamespaces(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClasses(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getInterfaces(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getTraits(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getExceptions(): array;

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctions(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassElements(): array;

    /**
     * @return mixed[]
     */
    public function getElements(): array;
}

<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Broker;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use TokenReflection\Broker\Backend\Memory;

interface BackendInterface
{
    /**
     * @param int $type Returned class types (multiple values may be OR-ed).
     * @return ClassReflectionInterface[]
     */
    public function getClasses($type = Memory::TOKENIZED_CLASSES): array;

    /**
     * @return ConstantReflectionInterface[]
     */
    public function getConstants(): array;


    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctions(): array;
}

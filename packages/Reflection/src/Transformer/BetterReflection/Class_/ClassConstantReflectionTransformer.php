<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\DocBlock\DocBlockFactory;
use ApiGen\Reflection\Reflection\Class_\ClassConstantReflection;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;

final class ClassConstantReflectionTransformer implements TransformerInterface
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    public function __construct(DocBlockFactory $docBlockFactory)
    {
        $this->docBlockFactory = $docBlockFactory;
    }

    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        if (! $reflection instanceof ReflectionClassConstant ||
           ($reflection->getDeclaringClass()->isInterface() ||
            $reflection->getDeclaringClass()->isTrait())
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param object|ReflectionClassConstant $reflection
     */
    public function transform($reflection): ClassConstantReflectionInterface
    {
        $docBlock = $this->docBlockFactory->createFromBetterReflection($reflection);

        return new ClassConstantReflection($reflection, $docBlock);
    }
}

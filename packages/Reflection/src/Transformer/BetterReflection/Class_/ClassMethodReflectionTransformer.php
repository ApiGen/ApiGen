<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\DocBlock\DocBlockFactory;
use ApiGen\Reflection\Reflection\Class_\ClassMethodReflection;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class ClassMethodReflectionTransformer implements TransformerInterface
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
        if (! $reflection instanceof ReflectionMethod) {
            return false;
        }

        $declaringClassOrTrait = $reflection->getDeclaringClass();

        return ! $declaringClassOrTrait->isTrait() && ! $declaringClassOrTrait->isInterface();
    }

    /**
     * @param ReflectionMethod|object $reflection
     */
    public function transform($reflection): ClassMethodReflectionInterface
    {
        return new ClassMethodReflection(
            $reflection,
            $this->docBlockFactory->createFromBetterReflection($reflection)
        );
    }
}

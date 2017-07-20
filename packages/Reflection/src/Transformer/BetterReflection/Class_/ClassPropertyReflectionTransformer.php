<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\DocBlock\DocBlockFactory;
use ApiGen\Reflection\Reflection\Class_\ClassPropertyReflection;
use Roave\BetterReflection\Reflection\ReflectionProperty;

final class ClassPropertyReflectionTransformer implements TransformerInterface
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
        if (! $reflection instanceof ReflectionProperty) {
            return false;
        }

        $declaringClassOrTrait = $reflection->getDeclaringClass();
        if ($declaringClassOrTrait === null) {
            return false;
        }

        return ! $declaringClassOrTrait->isTrait();
    }

    /**
     * @param object|ReflectionProperty $reflection
     */
    public function transform($reflection): ClassPropertyReflectionInterface
    {
        return new ClassPropertyReflection(
            $reflection,
            $this->docBlockFactory->createFromBetterReflection($reflection)
        );
    }
}

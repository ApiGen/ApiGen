<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Trait_;

use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\DocBlock\DocBlockFactory;
use ApiGen\Reflection\Reflection\Trait_\TraitMethodReflection;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class TraitMethodReflectionTransformer implements TransformerInterface
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

        return $declaringClassOrTrait->isTrait();
    }

    /**
     * @param object|ReflectionMethod $reflection
     */
    public function transform($reflection): TraitMethodReflectionInterface
    {
        return new TraitMethodReflection(
            $reflection,
            $this->docBlockFactory->createFromBetterReflection($reflection)
        );
    }
}

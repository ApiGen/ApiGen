<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Trait_;

use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\DocBlock\DocBlockFactory;
use ApiGen\Reflection\Reflection\Trait_\TraitPropertyReflection;
use Roave\BetterReflection\Reflection\ReflectionProperty;

final class TraitPropertyReflectionTransformer implements TransformerInterface
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
     * @param ReflectionProperty $reflection
     */
    public function matches($reflection): bool
    {
        if (! $reflection instanceof ReflectionProperty) {
            return false;
        }

        return $reflection->getDeclaringClass()->isTrait();
    }

    /**
     * @param object|ReflectionProperty $reflection
     */
    public function transform($reflection): TraitPropertyReflectionInterface
    {
        return new TraitPropertyReflection(
            $reflection,
            $this->docBlockFactory->createFromBetterReflection($reflection)
        );
    }
}

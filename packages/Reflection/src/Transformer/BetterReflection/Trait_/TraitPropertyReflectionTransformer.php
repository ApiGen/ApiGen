<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Trait_;

use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Reflection\Trait_\TraitPropertyReflection;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Roave\BetterReflection\Reflection\ReflectionProperty;

final class TraitPropertyReflectionTransformer implements TransformerInterface
{
    /**
     * @var DocBlockFactoryInterface
     */
    private $docBlockFactory;

    public function __construct(DocBlockFactoryInterface $docBlockFactory)
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

        return $declaringClassOrTrait->isTrait();
    }

    /**
     * @param object|ReflectionProperty $reflection
     */
    public function transform($reflection): TraitPropertyReflectionInterface
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() ?: ' ');

        return new TraitPropertyReflection($reflection, $docBlock);
    }
}

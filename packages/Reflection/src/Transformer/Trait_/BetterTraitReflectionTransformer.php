<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\Trait_;

use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Reflection\Trait_\TraitReflection;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class BetterTraitReflectionTransformer implements TransformerInterface
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
        return $reflection instanceof ReflectionClass && $reflection->isTrait();
    }

    /**
     * @param ReflectionClass $reflection
     */
    public function transform($reflection): TraitReflectionInterface
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() ?: ' ');
        return new TraitReflection($reflection, $docBlock);
    }
}

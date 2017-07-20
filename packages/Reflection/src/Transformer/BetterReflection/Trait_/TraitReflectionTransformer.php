<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Trait_;

use ApiGen\Element\Tree\TraitUsersResolver;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\SortableTransformerInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Reflection\Trait_\TraitReflection;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class TraitReflectionTransformer implements TransformerInterface, SortableTransformerInterface
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    /**
     * @var TraitUsersResolver
     */
    private $traitUsersResolver;

    public function __construct(DocBlockFactory $docBlockFactory, TraitUsersResolver $traitUsersResolver)
    {
        $this->docBlockFactory = $docBlockFactory;
        $this->traitUsersResolver = $traitUsersResolver;
    }

    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof ReflectionClass && $reflection->isTrait();
    }

    /**
     * @param ReflectionClass|object $reflection
     */
    public function transform($reflection): TraitReflectionInterface
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() ?: ' ');

        return new TraitReflection($reflection, $docBlock, $this->traitUsersResolver);
    }
}

<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\ElementReflection\Reflection\TraitReflection;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
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
    public function transform($reflection): TraitReflection
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() . ' ');
        return new TraitReflection($reflection, $docBlock);
    }
}

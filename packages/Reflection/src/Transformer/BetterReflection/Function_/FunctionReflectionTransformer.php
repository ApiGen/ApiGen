<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Function_;

use ApiGen\Reflection\Contract\Transformer\SortableTransformerInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Reflection\Function_\FunctionReflection;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionFunction as BetterReflectionFunction;

final class FunctionReflectionTransformer implements TransformerInterface, SortableTransformerInterface
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
        return $reflection instanceof BetterReflectionFunction;
    }

    /**
     * @param object|BetterReflectionFunction $reflection
     */
    public function transform($reflection): FunctionReflection
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() ?: ' ');

        return new FunctionReflection($reflection, $docBlock);
    }
}

<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Function_;

use ApiGen\Reflection\Reflection\FunctionReflection;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionFunction as BetterReflectionFunction;

final class FunctionReflectionTransformer implements TransformerInterface
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
     * @param BetterReflectionFunction $reflection
     */
    public function transform($reflection): FunctionReflection
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() ?: ' ');

        return new FunctionReflection($reflection, $docBlock);
    }
}

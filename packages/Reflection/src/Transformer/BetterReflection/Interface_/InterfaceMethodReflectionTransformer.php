<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Interface_;

use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Reflection\Interface_\InterfaceMethodReflection;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionFunction as BetterReflectionFunction;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class InterfaceMethodReflectionTransformer implements TransformerInterface
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
        return $reflection instanceof ReflectionMethod
            && $reflection->getDeclaringClass()->isInterface();
    }

    /**
     * @param object|BetterReflectionFunction $reflection
     */
    public function transform($reflection): InterfaceMethodReflection
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() ?: ' ');

        return new InterfaceMethodReflection($reflection, $docBlock);
    }
}

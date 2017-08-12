<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceConstantReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\DocBlock\DocBlockFactory;
use ApiGen\Reflection\Reflection\Interface_\InterfaceConstantReflection;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;

final class InterfaceConstantReflectionTransformer implements TransformerInterface
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
        if (! $reflection instanceof ReflectionClassConstant ||
            ! $reflection->getDeclaringClass()->isInterface()
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param object|ReflectionClassConstant $reflection
     */
    public function transform($reflection): InterfaceConstantReflectionInterface
    {
        $docBlock = $this->docBlockFactory->createFromBetterReflection($reflection);

        return new InterfaceConstantReflection($reflection, $docBlock);
    }
}

<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Reflection\Class_\ClassConstantReflection;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;

final class ClassConstantReflectionTransformer implements TransformerInterface
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
        return !(!$reflection instanceof ReflectionClassConstant ||
            ($reflection->getDeclaringClass()->isInterface() ||
                $reflection->getDeclaringClass()->isTrait()));
    }

    /**
     * @param ReflectionClassConstant $reflection
     */
    public function transform($reflection): ClassConstantReflectionInterface
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() ?: ' ');

        return new ClassConstantReflection($reflection, $docBlock);
    }
}

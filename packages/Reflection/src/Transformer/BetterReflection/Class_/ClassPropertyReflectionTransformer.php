<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Reflection\Class_\ClassPropertyReflection;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Roave\BetterReflection\Reflection\ReflectionProperty;

final class ClassPropertyReflectionTransformer implements TransformerInterface
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

        $declaringClass = $reflection->getDeclaringClass();
        if ($declaringClass === null) {
            return false;
        }

        return ! $declaringClass->isTrait();
    }

    /**
     * @param ReflectionProperty $reflection
     */
    public function transform($reflection): ClassPropertyReflectionInterface
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocBlockTypes() ?: ' ');

        return new ClassPropertyReflection($reflection, $docBlock);
    }
}

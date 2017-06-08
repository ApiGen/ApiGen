<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Reflection\Class_\ClassMethodReflection;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Types\ContextFactory;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class ClassMethodReflectionTransformer implements TransformerInterface
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
        if (! $reflection instanceof ReflectionMethod) {
            return false;
        }

        $declaringClassOrTrait = $reflection->getDeclaringClass();

        return ! $declaringClassOrTrait->isTrait() && ! $declaringClassOrTrait->isInterface();
    }

    /**
     * @param ReflectionMethod|object $reflection
     */
    public function transform($reflection): ClassMethodReflectionInterface
    {
        $docBlock = $this->createDocBlockFromReflection($reflection);

        return new ClassMethodReflection($reflection, $docBlock);
    }

    private function createDocBlockFromReflection(ReflectionMethod $reflection): DocBlock
    {
        return $this->docBlockFactory->create($reflection->getDocComment() ?: ' ');
    }
}

<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Reflection\Class_\ClassPropertyReflection;
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

        $declaringClassOrTrait = $reflection->getDeclaringClass();
        if ($declaringClassOrTrait === null) {
            return false;
        }

        return ! $declaringClassOrTrait->isTrait();
    }

    public function transform(ReflectionProperty $reflection): ClassPropertyReflectionInterface
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() ?: ' ');

        return new ClassPropertyReflection($reflection, $docBlock);
    }
}

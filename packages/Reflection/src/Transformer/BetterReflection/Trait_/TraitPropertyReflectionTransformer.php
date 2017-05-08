<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Trait_;

use ApiGen\Reflection\Reflection\Method\MethodParameterReflection;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Roave\BetterReflection\Reflection\ReflectionParameter;
use Roave\BetterReflection\Reflection\ReflectionProperty;

final class TraitPropertyReflectionTransformer implements TransformerInterface
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

        return $declaringClass->isTrait();
    }

    /**
     * @param ReflectionParameter $reflection
     */
    public function transform($reflection): MethodParameterReflection
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocBlockTypes() ?: ' ');

        return new MethodParameterReflection($reflection, $docBlock);
    }
}

<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Method;

use ApiGen\Reflection\Reflection\MethodParameterReflection;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class MethodParameterReflectionTransformer implements TransformerInterface
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
        if ( !$reflection instanceof ReflectionParameter) {
            return false;
        }

        return (bool) $reflection->getDeclaringClass();
    }

    /**
     * @param ReflectionParameter $reflection
     */
    public function transform($reflection): MethodParameterReflection
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocBlockTypes() . ' ');

        return new MethodParameterReflection($reflection, $docBlock);
    }
}

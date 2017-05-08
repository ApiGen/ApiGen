<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Function_;

use ApiGen\Reflection\Contract\Reflection\FunctionParameterReflectionInterface;
use ApiGen\Reflection\Reflection\FunctionParameterReflection;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class FunctionParameterReflectionTransformer implements TransformerInterface
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
        if (! $reflection instanceof ReflectionParameter) {
            return false;
        }

        return $reflection->getDeclaringClass() === null;
    }

    /**
     * @param ReflectionParameter $reflection
     */
    public function transform($reflection): FunctionParameterReflectionInterface
    {
        return new FunctionParameterReflection($reflection);
    }
}

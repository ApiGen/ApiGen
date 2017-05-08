<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer;

use ApiGen\Reflection\Contract\Reflection\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Reflection\ClassReflection;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class BetterConstantReflectionTransformer implements TransformerInterface
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
     * @param object|string $reflection
     */
    public function matches($reflection): bool
    {
        return is_string($reflection);
    }

    /**
     * @param string $reflection
     */
    public function transform($reflection): ClassConstantReflectionInterface
    {
        dump($reflection);

        // @todo: find out here!

        die;
//        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() . ' ');
//
//        return new ClassReflection($reflection, $docBlock);
    }
}

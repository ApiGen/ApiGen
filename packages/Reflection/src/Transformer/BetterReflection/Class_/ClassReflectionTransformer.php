<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Reflection\Class_\ClassReflection;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Tree\ParentClassElementsResolver;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class ClassReflectionTransformer implements TransformerInterface
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    /**
     * @var ParentClassElementsResolver
     */
    private $parentClassElementsResolver;

    public function __construct(DocBlockFactory $docBlockFactory, ParentClassElementsResolver $parentClassElementsResolver)
    {
        $this->docBlockFactory = $docBlockFactory;
        $this->parentClassElementsResolver = $parentClassElementsResolver;
    }

    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof ReflectionClass && ! $reflection->isTrait() && ! $reflection->isInterface();
    }

    /**
     * @param object|ReflectionClass $reflection
     */
    public function transform($reflection): ClassReflectionInterface
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() ?: ' ');

        return new ClassReflection($reflection, $docBlock, $this->parentClassElementsResolver);
    }
}

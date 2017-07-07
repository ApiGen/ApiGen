<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Class_;

use ApiGen\Element\Tree\ParentClassElementsResolver;
use ApiGen\Element\Tree\SubClassesResolver;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\SortableTransformerInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Reflection\Class_\ClassReflection;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class ClassReflectionTransformer implements TransformerInterface, SortableTransformerInterface
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    /**
     * @var ParentClassElementsResolver
     */
    private $parentClassElementsResolver;

    /**
     * @var SubClassesResolver
     */
    private $subClassesResolver;

    public function __construct(
        DocBlockFactory $docBlockFactory,
        ParentClassElementsResolver $parentClassElementsResolver,
        SubClassesResolver $subClassesResolver
    ) {
        $this->docBlockFactory = $docBlockFactory;
        $this->parentClassElementsResolver = $parentClassElementsResolver;
        $this->subClassesResolver = $subClassesResolver;
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

        return new ClassReflection(
            $reflection,
            $docBlock,
            $this->parentClassElementsResolver,
            $this->subClassesResolver
        );
    }
}

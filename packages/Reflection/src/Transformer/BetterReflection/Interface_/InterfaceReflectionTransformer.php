<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Interface_;

use ApiGen\Element\Tree\ImplementersResolver;
use ApiGen\Reflection\Contract\Transformer\SortableTransformerInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Reflection\Interface_\InterfaceReflection;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class InterfaceReflectionTransformer implements TransformerInterface, SortableTransformerInterface
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    /**
     * @var ImplementersResolver
     */
    private $implementersResolver;

    public function __construct(DocBlockFactory $docBlockFactory, ImplementersResolver $implementersResolver)
    {
        $this->docBlockFactory = $docBlockFactory;
        $this->implementersResolver = $implementersResolver;
    }

    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof ReflectionClass && $reflection->isInterface();
    }

    /**
     * @param object|ReflectionClass $reflection
     */
    public function transform($reflection): InterfaceReflection
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() ?: ' ');

        return new InterfaceReflection($reflection, $docBlock, $this->implementersResolver);
    }
}

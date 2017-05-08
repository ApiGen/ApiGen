<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\Native\Class_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use phpDocumentor\Reflection\DocBlockFactory;

final class InterfaceConstantReflectionTransformer implements TransformerInterface
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
        dump($reflection);
        die;

        return is_string($reflection);
    }

    /**
     * @param string $reflection
     */
    public function transform($reflection): ClassConstantReflectionInterface
    {
        // what to do here? :)
        dump($reflection);
        dump(__CLASS__);

        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() ?: ' ');

        return new ClassReflection($reflection, $docBlock);
    }
}

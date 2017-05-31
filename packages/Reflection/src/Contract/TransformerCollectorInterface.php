<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract;

use ApiGen\Reflection\Contract\Transformer\TransformerInterface;

interface TransformerCollectorInterface
{
    public function addTransformer(TransformerInterface $transformer): void;

    /**
     * @param object $reflection
     * @return object
     */
    public function transformSingle($reflection);

    /**
     * @param object[] $reflections
     * @return object[]
     */
    public function transformGroup(array $reflections): array;
}

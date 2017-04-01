<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Contract;

use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;

interface TransformerCollectorInterface
{
    public function addTransformer(TransformerInterface $transformer): void;

    /**
     * @param object $reflection
     * @return mixed
     */
    public function transformReflectionToElement($reflection);
}

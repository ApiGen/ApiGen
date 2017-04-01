<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Contract\Transformer;

interface TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool;

    /**
     * @param object $reflection
     * @return mixed
     */
    public function transform($reflection);
}

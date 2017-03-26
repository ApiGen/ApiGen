<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

interface NamespaceSorterInterface
{
    /**
     * @param mixed[] $namespaces
     * @return mixed[]
     */
    public function sort(array $namespaces): array;
}

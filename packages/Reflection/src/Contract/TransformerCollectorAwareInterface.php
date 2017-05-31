<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract;

interface TransformerCollectorAwareInterface
{
    public function setTransformerCollector(TransformerCollectorInterface $transformerCollector): void;
}

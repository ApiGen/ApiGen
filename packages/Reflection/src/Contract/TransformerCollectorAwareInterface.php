<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract;

use ApiGen\Reflection\TransformerCollector;

interface TransformerCollectorAwareInterface
{
    public function setTransformerCollector(TransformerCollector $transformerCollector): void;
}

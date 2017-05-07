<?php declare(strict_types=1);

namespace ApiGen\Element\Contract;

interface ElementExtractorInterface
{
    /**
     * @return mixed[]
     */
    public function extractElementsByAnnotation(string $annotation): array;
}

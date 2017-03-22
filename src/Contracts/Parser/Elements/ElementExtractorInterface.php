<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

interface ElementExtractorInterface
{
    /**
     * @return mixed[]
     */
    public function extractElementsByAnnotation(string $annotation): array;
}

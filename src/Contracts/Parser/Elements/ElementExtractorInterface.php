<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

interface ElementExtractorInterface
{
    public function extractElementsByAnnotation(string $annotation, callable $skipClassCallback = null): array;
}

<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

interface ElementExtractorInterface
{

    /**
     * @param string $annotation
     * @param callable $skipClassCallback
     * @return array[]
     */
    public function extractElementsByAnnotation(string $annotation, callable $skipClassCallback = null);
}

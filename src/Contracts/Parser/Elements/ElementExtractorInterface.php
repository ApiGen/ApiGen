<?php

namespace ApiGen\Contracts\Parser\Elements;

interface ElementExtractorInterface
{

    /**
     * @param string $annotation
     * @param callable $skipClassCallback
     * @return array[]
     */
    public function extractElementsByAnnotation($annotation, callable $skipClassCallback = null);
}

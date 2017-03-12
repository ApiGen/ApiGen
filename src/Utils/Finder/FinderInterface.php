<?php

namespace ApiGen\Utils\Finder;

interface FinderInterface
{

    /**
     * @param string|array $source
     * @param array $exclude
     * @param array $extensions
     * @return \SplFileInfo
     */
    public function find($source, array $exclude = [], array $extensions = ['php']);
}

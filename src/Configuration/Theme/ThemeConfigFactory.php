<?php

namespace ApiGen\Configuration\Theme;

interface ThemeConfigFactory
{

    /**
     * @param string $filePath
     * @return ThemeConfig
     */
    public function create($filePath);
}

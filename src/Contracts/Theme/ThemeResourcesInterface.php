<?php

namespace ApiGen\Contracts\Theme;

interface ThemeResourcesInterface
{

    /**
     * @param string $destination
     */
    public function copyToDestination($destination);
}

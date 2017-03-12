<?php

namespace ApiGen\Contracts\Generator\Resolvers;

interface RelativePathResolverInterface
{

    /**
     * @param string $file
     * @return string
     */
    public function getRelativePath($file);
}

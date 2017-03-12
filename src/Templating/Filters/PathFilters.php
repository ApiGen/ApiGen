<?php

namespace ApiGen\Templating\Filters;

use ApiGen\Generator\Resolvers\RelativePathResolver;

class PathFilters extends Filters
{

    /**
     * @var RelativePathResolver
     */
    private $relativePathResolver;


    public function __construct(RelativePathResolver $relativePathResolver)
    {
        $this->relativePathResolver = $relativePathResolver;
    }


    /**
     * @param string $fileName
     * @return string
     */
    public function relativePath($fileName)
    {
        return $this->relativePathResolver->getRelativePath($fileName);
    }
}

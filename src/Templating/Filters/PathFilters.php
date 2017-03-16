<?php declare(strict_types=1);

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


    public function relativePath(string $fileName): string
    {
        return $this->relativePathResolver->getRelativePath($fileName);
    }
}

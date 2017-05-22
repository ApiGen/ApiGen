<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Generator\Resolvers\RelativePathResolver;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class PathFilters implements LatteFiltersProviderInterface
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
     * @return callable[]
     */
    public function getFilters(): array
    {
        // @todo: possibly drop, not added value to display the name manually,
        // already in url!
        return [
            'relativePath' => function (string $fileName): string {
                return $this->relativePathResolver->getRelativePath($fileName);
            }
        ];
    }
}

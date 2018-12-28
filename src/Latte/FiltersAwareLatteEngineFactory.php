<?php declare(strict_types=1);

namespace ApiGen\Latte;

use ApiGen\Contract\Templating\FilterProviderInterface;
use Latte\Engine;

final class FiltersAwareLatteEngineFactory
{
    /**
     * @var FilterProviderInterface[]
     */
    private $filterProviders = [];

    public function addFilterProvider(FilterProviderInterface $filterProvider): void
    {
        $this->filterProviders[] = $filterProvider;
    }

    public function create(): Engine
    {
        $latteEngine = new Engine;
        $latteEngine->setTempDirectory(sys_get_temp_dir() . '/_latte_cache');

        foreach ($this->filterProviders as $filterProvider) {
            foreach ($filterProvider->getFilters() as $name => $callback) {
                $latteEngine->addFilter($name, $callback);
            }
        }

        return $latteEngine;
    }
}

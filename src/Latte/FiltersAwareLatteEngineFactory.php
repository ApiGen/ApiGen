<?php declare(strict_types=1);

namespace ApiGen\Latte;

use ApiGen\Contract\Templating\FilterProviderInterface;
use Latte\Engine;

final class FiltersAwareLatteEngineFactory
{
    /**
     * @var FilterProviderInterface[]
     */
    private $filtersProviders = [];

    public function addFiltersProvider(FilterProviderInterface $filtersProvider): void
    {
        $this->filtersProviders[] = $filtersProvider;
    }

    public function create(): Engine
    {
        $latteEngine = new Engine;
        $latteEngine->setTempDirectory(sys_get_temp_dir() . '/_latte_cache');

        foreach ($this->filtersProviders as $filtersProvider) {
            foreach ($filtersProvider->getFilters() as $name => $callback) {
                $latteEngine->addFilter($name, $callback);
            }
        }

        return $latteEngine;
    }
}

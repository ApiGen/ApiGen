<?php declare(strict_types=1);

namespace ApiGen\Latte;

use Latte\Engine;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class FiltersAwareLatteEngineFactory
{
    /**
     * @var LatteFiltersProviderInterface[]
     */
    private $filtersProviders = [];

    public function addFiltersProvider(LatteFiltersProviderInterface $filtersProvider): void
    {
        $this->filtersProviders[] = $filtersProvider;
    }

    public function create(): Engine
    {
        $latteEngine = new Engine;
        foreach ($this->filtersProviders as $filtersProvider) {
            foreach ($filtersProvider->getFilters() as $name => $callback) {
                $latteEngine->addFilter($name, $callback);
            }
        }

        return $latteEngine;
    }
}
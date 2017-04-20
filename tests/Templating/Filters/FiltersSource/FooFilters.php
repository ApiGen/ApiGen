<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters\FiltersSource;

use ApiGen\Templating\Filters\Filters;

final class FooFilters extends Filters
{
    protected function bazFilter(string $text): string
    {
        return 'Filtered: ' . $text;
    }
}

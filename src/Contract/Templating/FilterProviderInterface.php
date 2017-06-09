<?php declare(strict_types=1);

namespace ApiGen\Contract\Templating;

interface FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFilters(): array;
}

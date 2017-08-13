<?php declare(strict_types=1);

namespace ApiGen\Element\Latte\Filter;

use ApiGen\Contract\Templating\FilterProviderInterface;
use ApiGen\Utils\DefaultValueDumper;

final class DumpDefaultValueFilter implements FilterProviderInterface
{
    /**
     * @var DefaultValueDumper
     */
    private $defaultValueDumper;

    public function __construct(DefaultValueDumper $defaultValueDumper)
    {
        $this->defaultValueDumper = $defaultValueDumper;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            // use in .latte: {$property->getDefaultValue()|dumpDefaultValue}
            'dumpDefaultValue' => function ($value) {
                return $this->defaultValueDumper->dumpValue($value);
            },
        ];
    }
}

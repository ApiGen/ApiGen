<?php declare(strict_types=1);

namespace ApiGen\Element\Latte\Filter;

use ApiGen\Contract\Templating\FilterProviderInterface;
use ApiGen\SourceCodeHighlighter\SourceCodeHighlighter;

final class DumpDefaultValueFilter implements FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            // use in .latte: {$property->getDefaultValue()|dumpDefaultValue}
            'dumpDefaultValue' => function ($value) {
                return var_export($value, true);
            }
        ];
    }
}

<?php declare(strict_types=1);

namespace ApiGen\Element\Latte\Filter;

use ApiGen\Contract\Templating\FilterProviderInterface;
use ApiGen\SourceCodeHighlighter\SourceCodeHighlighter;

final class PhpHighlightFilter implements FilterProviderInterface
{
    /**
     * @var SourceCodeHighlighter
     */
    private $highlighter;

    public function __construct(SourceCodeHighlighter $highlighter)
    {
        $this->highlighter = $highlighter;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            // use in .latte: {$method|phpHighlight}
            'phpHighlight' => function ($code) {
                return $this->highlighter->highlight($code);
            },
        ];
    }
}

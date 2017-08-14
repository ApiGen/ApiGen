<?php declare(strict_types=1);

namespace ApiGen\SourceCodeHighlighter;

use FSHL\Highlighter;

final class SourceCodeHighlighter
{
    /**
     * @var Highlighter
     */
    private $highlighter;

    public function __construct(Highlighter $highlighter)
    {
        $this->highlighter = $highlighter;
    }

    public function highlight(string $sourceCode): string
    {
        $this->highlighter->setOptions(Highlighter::OPTION_TAB_INDENT);

        return $this->highlighter->highlight($sourceCode);
    }

    public function highlightAndAddLineNumbers(string $sourceCode): string
    {
        $this->highlighter->setOptions(Highlighter::OPTION_TAB_INDENT | Highlighter::OPTION_LINE_COUNTER);

        return $this->highlighter->highlight($sourceCode);
    }
}

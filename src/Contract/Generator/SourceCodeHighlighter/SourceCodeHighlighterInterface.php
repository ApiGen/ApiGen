<?php declare(strict_types=1);

namespace ApiGen\Contract\Generator\SourceCodeHighlighter;

interface SourceCodeHighlighterInterface
{
    public function highlight(string $sourceCode): string;

    public function highlightAndAddLineNumbers(string $sourceCode): string;
}

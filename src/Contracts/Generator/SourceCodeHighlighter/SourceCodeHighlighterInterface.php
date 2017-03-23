<?php declare(strict_types=1);

namespace ApiGen\Contracts\Generator\SourceCodeHighlighter;

interface SourceCodeHighlighterInterface
{
    public function highlight(string $sourceCode): string;

    public function highlightAndAddLineNumbers(string $sourceCode): string;
}

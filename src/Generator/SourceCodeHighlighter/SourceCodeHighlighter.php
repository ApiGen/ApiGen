<?php declare(strict_types=1);

namespace ApiGen\Generator\SourceCodeHighlighter;

interface SourceCodeHighlighter
{
    public function highlight(string $sourceCode): string;

    public function highlightAndAddLineNumbers(string $sourceCode): string;
}

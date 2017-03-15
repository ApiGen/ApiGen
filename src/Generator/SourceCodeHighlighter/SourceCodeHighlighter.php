<?php declare(strict_types=1);

namespace ApiGen\Generator\SourceCodeHighlighter;

interface SourceCodeHighlighter
{

    /**
     * Highlights passed code
     *
     * @param string $sourceCode
     * @return string
     */
    public function highlight($sourceCode);


    /**
     * Highlights passed code an adds line number at the beginning.
     *
     * @param string $sourceCode
     * @return string
     */
    public function highlightAndAddLineNumbers($sourceCode);
}

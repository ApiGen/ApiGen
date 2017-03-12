<?php

namespace ApiGen\Contracts\Markup\PhpCodeHighlighter;

interface PhpCodeHighlighterInterface
{

    /**
     * Highlights php code.
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

<?php

namespace ApiGen\Generator\SourceCodeHighlighter;

use FSHL\Highlighter;

class FshlSourceCodeHighlighter implements SourceCodeHighlighter
{

    /**
     * @var Highlighter
     */
    private $highlighter;


    public function __construct(Highlighter $highlighter)
    {
        $this->highlighter = $highlighter;
    }


    /**
     * @param string $sourceCode
     * @return string
     */
    public function highlight($sourceCode)
    {
        $this->highlighter->setOptions(Highlighter::OPTION_TAB_INDENT);
        return $this->highlighter->highlight($sourceCode);
    }


    /**
     * @param string $sourceCode
     * @return string
     */
    public function highlightAndAddLineNumbers($sourceCode)
    {
        $this->highlighter->setOptions(Highlighter::OPTION_TAB_INDENT | Highlighter::OPTION_LINE_COUNTER);
        return $this->highlighter->highlight($sourceCode);
    }
}

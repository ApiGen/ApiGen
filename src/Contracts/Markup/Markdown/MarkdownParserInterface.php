<?php

namespace ApiGen\Contracts\Markup\Markdown;

interface MarkdownParserInterface
{

    /**
     * @param string $content
     * @return string
     */
    public function parse($content);
}

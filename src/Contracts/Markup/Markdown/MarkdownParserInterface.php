<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Markup\Markdown;

interface MarkdownParserInterface
{

    /**
     * @param string $content
     * @return string
     */
    public function parse($content);
}

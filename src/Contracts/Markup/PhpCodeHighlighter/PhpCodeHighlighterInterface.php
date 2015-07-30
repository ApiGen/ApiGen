<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

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

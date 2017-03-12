<?php

namespace ApiGen\Generator\Markups;

interface Markup
{

    /**
     * @param string $text
     * @return string
     */
    public function line($text);


    /**
     * @param string $text
     * @return string
     */
    public function block($text);
}

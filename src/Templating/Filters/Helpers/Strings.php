<?php

namespace ApiGen\Templating\Filters\Helpers;

class Strings
{

    /**
     * @param string $value
     * @return array
     */
    public static function split($value)
    {
        return preg_split('~\s+|$~', $value, 2);
    }
}

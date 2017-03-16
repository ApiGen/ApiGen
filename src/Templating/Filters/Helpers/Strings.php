<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters\Helpers;

class Strings
{
    public static function split(string $value): array
    {
        return preg_split('~\s+|$~', $value, 2);
    }
}

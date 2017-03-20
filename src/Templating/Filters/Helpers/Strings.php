<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters\Helpers;

final class Strings
{
    /**
     * @return string[]
     */
    public static function split(string $value): array
    {
        return preg_split('~\s+|$~', $value, 2);
    }
}

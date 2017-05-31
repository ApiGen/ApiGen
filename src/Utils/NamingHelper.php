<?php declare(strict_types=1);

namespace ApiGen\Utils;

final class NamingHelper
{
    public static function nameToFilePath(string $name): string
    {
        return preg_replace('~[^\w]~', '.', $name);
    }
}

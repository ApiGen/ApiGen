<?php declare(strict_types=1);

namespace ApiGen\Utils;

final class DefaultValueDumper
{
    /**
     * @param mixed
     */
    public function dumpValue($value): string
    {
        // @todo - there is a known issue - when dumped array, there is trailing
        // comma after last parameter.
        return var_export($value, true);
    }
}

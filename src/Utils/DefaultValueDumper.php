<?php declare(strict_types=1);

namespace ApiGen\Utils;

use Latte\Runtime\Filters;
use Nette\Utils\Html;

final class DefaultValueDumper
{
    /**
     * @param mixedt
     */
    public function dumpValue($value): string
    {
        // @todo - there is a known issue - when dumped array, there is trailing
        // comma after last parameter.
        return var_export($value, true);
    }
}

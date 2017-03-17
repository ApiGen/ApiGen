<?php declare(strict_types=1);

namespace ApiGen\Tests;

class DeprecatedMethod
{
    /**
     * @deprecated
     */
    public function getDrink(): string
    {
        return 'water';
    }
}

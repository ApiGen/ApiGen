<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\DeprecatedSources;

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

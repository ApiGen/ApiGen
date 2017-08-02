<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\DeprecatedSources;

/**
 * @deprecated
 */
class DeprecatedClass
{
    public function getDrink(): string
    {
        return 'Mojito';
    }
}

<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\DeprecatedSources;

/**
 * @deprecated
 */
class DeprecatedClass
{
    /**
     * @return string
     */
    public function getDrink()
    {
        return 'Mojito';
    }
}

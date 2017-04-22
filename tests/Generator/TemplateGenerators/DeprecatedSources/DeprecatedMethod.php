<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\TemplateGenerators\DeprecatedSources;

class DeprecatedMethod
{
    /**
     * @deprecated
     * @return string
     */
    public function getDrink()
    {
        return 'water';
    }
}

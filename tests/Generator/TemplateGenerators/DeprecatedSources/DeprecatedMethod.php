<?php

namespace ApiGen\Tests;

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

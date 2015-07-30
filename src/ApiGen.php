<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;

class ApiGen
{

    /**
     * @var string
     */
    const VERSION = '4.2.0-dev';


    /**
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }
}

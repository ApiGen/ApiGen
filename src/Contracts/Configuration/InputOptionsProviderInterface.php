<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Configuration;

interface InputOptionsProviderInterface
{

    /**
     * @return array
     */
    public function getOptions();
}

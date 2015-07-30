<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Console\Helper;

interface ProgressBarInterface
{

    /**
     * @param int $maximum
     */
    public function init($maximum = 1);


    /**
     * @param int $increment
     */
    public function increment($steps = 1);
}

<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Console\Helper;

interface ProgressBarFacadeInterface
{

    /**
     * @param int $maximum
     */
    public function init($maximum);


    /**
     * @param int $step
     */
    public function advance($step = 1);
}

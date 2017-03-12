<?php

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

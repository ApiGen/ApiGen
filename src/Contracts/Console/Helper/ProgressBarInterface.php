<?php declare(strict_types=1);

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

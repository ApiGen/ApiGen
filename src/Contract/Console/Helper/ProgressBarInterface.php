<?php declare(strict_types=1);

namespace ApiGen\Contract\Console\Helper;

interface ProgressBarInterface
{
    public function init(int $maximum = 1): void;

    public function increment(int $steps = 1): void;
}

<?php declare(strict_types=1);

namespace ApiGen\Contracts\Generator;

interface StepCounterInterface
{

    /**
     * @return int
     */
    public function getStepCount();
}

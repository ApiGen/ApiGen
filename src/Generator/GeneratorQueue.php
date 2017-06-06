<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Console\Helper\ProgressBar;
use ApiGen\Console\Progress\StepCounter;
use ApiGen\Contract\Generator\GeneratorInterface;

final class GeneratorQueue
{
    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * @var GeneratorInterface[]
     */
    private $generators = [];

    /**
     * @var StepCounter
     */
    private $stepCounter;

    public function __construct(ProgressBar $progressBar, StepCounter $stepCounter)
    {
        $this->progressBar = $progressBar;
        $this->stepCounter = $stepCounter;
    }

    public function addGenerator(GeneratorInterface $generator): void
    {
        $this->generators[] = $generator;
    }

    public function run(): void
    {
        $this->progressBar->init($this->stepCounter->getStepCount());

        foreach ($this->generators as $generator) {
            $generator->generate();
        }
    }
}

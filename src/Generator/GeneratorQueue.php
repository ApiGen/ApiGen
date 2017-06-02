<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Console\Progress\StepCounter;
use ApiGen\Contract\Console\Helper\ProgressBarInterface;
use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Contract\Generator\GeneratorQueueInterface;

final class GeneratorQueue implements GeneratorQueueInterface
{
    /**
     * @var ProgressBarInterface
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

    public function __construct(ProgressBarInterface $progressBar, StepCounter $stepCounter)
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

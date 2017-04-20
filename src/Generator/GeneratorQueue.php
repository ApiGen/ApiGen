<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contracts\Console\Helper\ProgressBarInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Progress\StepCounter;

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

        foreach ($this->generators as $templateGenerator) {
            $templateGenerator->generate();
        }
    }
}

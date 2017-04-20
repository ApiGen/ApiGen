<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contracts\Console\Helper\ProgressBarInterface;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Progress\StepCounter;

final class GeneratorQueue implements GeneratorQueueInterface
{
    /**
     * @var ProgressBarInterface
     */
    private $progressBar;

    /**
     * @var TemplateGeneratorInterface[]
     */
    private $templateGenerators = [];

    /**
     * @var StepCounter
     */
    private $stepCounter;

    public function __construct(ProgressBarInterface $progressBar, StepCounter $stepCounter)
    {
        $this->progressBar = $progressBar;
        $this->stepCounter = $stepCounter;
    }

    public function addGenerator(TemplateGeneratorInterface $templateGenerator): void
    {
        $this->templateGenerators[] = $templateGenerator;
    }

    public function run(): void
    {
        $this->progressBar->init($this->stepCounter->getStepCount());

        foreach ($this->templateGenerators as $templateGenerator) {
            $templateGenerator->generate();
        }
    }
}

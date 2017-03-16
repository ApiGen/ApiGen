<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contracts\Console\Helper\ProgressBarInterface;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\ConditionalTemplateGeneratorInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;

class GeneratorQueue implements GeneratorQueueInterface
{

    /**
     * @var ProgressBarInterface
     */
    private $progressBar;

    /**
     * @var TemplateGeneratorInterface[]
     */
    private $queue = [];


    public function __construct(ProgressBarInterface $progressBar)
    {
        $this->progressBar = $progressBar;
    }


    public function run(): void
    {
        $this->progressBar->init($this->getStepCount());
        foreach ($this->getAllowedQueue() as $templateGenerator) {
            $templateGenerator->generate();
        }
    }


    public function addToQueue(TemplateGeneratorInterface $templateGenerator): void
    {
        $this->queue[] = $templateGenerator;
    }


    public function getQueue()
    {
        return $this->queue;
    }


    /**
     * @return TemplateGeneratorInterface[]
     */
    private function getAllowedQueue()
    {
        return array_filter($this->queue, function (TemplateGeneratorInterface $generator) {
            if ($generator instanceof ConditionalTemplateGeneratorInterface) {
                return $generator->isAllowed();
            } else {
                return true;
            }
        });
    }


    private function getStepCount(): int
    {
        $steps = 0;
        foreach ($this->getAllowedQueue() as $templateGenerator) {
            if ($templateGenerator instanceof StepCounterInterface) {
                $steps += $templateGenerator->getStepCount();
            }
        }
        return $steps;
    }
}

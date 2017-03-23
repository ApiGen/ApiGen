<?php declare(strict_types=1);

namespace ApiGen\Console\Helper;

use ApiGen\Contracts\Console\Helper\ProgressBarInterface;
use ApiGen\Contracts\Console\IO\IOInterface;
use Symfony\Component\Console\Helper\ProgressBar as ProgressBarHelper;

final class ProgressBar implements ProgressBarInterface
{
    /**
     * @var IOInterface
     */
    private $consoleIO;

    /**
     * @var ProgressBarHelper
     */
    private $bar;

    public function __construct(IOInterface $consoleIO)
    {
        $this->consoleIO = $consoleIO;
    }

    public function init(int $maximum = 1): void
    {
        $this->bar = new ProgressBarHelper($this->consoleIO->getOutput(), $maximum);
        $this->bar->setFormat($this->getBarFormat());
        $this->bar->start();
    }

    public function increment(int $increment = 1): void
    {
        if ($this->bar === null) {
            return;
        }

        $this->bar->advance($increment);
        if ($this->bar->getProgress() === $this->bar->getMaxSteps()) {
            $this->consoleIO->getOutput()->writeln(' - Finished!');
        }
    }

    private function getBarFormat(): string
    {
        if ($this->getDebugOption()) {
            return 'debug';
        } else {
            return '<comment>%percent:3s% %</comment>';
        }
    }

    private function getDebugOption(): bool
    {
        if ($this->consoleIO->getInput() && $this->consoleIO->getInput()->hasOption('debug')) {
            return (bool) $this->consoleIO->getInput()->getOption('debug');
        } else {
            return false;
        }
    }
}

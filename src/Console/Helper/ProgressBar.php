<?php declare(strict_types=1);

namespace ApiGen\Console\Helper;

use ApiGen\Contracts\Console\Helper\ProgressBarInterface;
use ApiGen\Contracts\Console\IO\IOInterface;
use Symfony\Component\Console\Helper\ProgressBar as ProgressBarHelper;

final class ProgressBar implements ProgressBarInterface
{
    /**
     * @var string
     */
    private const BAR_FORMAT = '<comment>%percent:3s% %</comment>';

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
        $this->bar->setFormat(self::BAR_FORMAT);
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
}

<?php declare(strict_types=1);

namespace ApiGen\Console\Helper;

use Symfony\Component\Console\Helper\ProgressBar as ProgressBarHelper;
use Symfony\Component\Console\Output\OutputInterface;

final class ProgressBar
{
    /**
     * @var string
     */
    private const BAR_FORMAT = 'debug';

    /**
     * @var ProgressBarHelper
     */
    private $bar;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function init(int $maximum = 1): void
    {
        $this->bar = new ProgressBarHelper($this->output, $maximum);
        $this->bar->setFormat(self::BAR_FORMAT);
        $this->bar->start();
    }

    public function increment(int $increment = 1): void
    {
        if ($this->bar === null) {
            return;
        }

        $this->bar->advance($increment);
    }

    public function __destruct()
    {
        if ($this->bar) {
            if ($this->bar->getProgress() === $this->bar->getMaxSteps()) {
                $msg = '<info>Done! API generation was successfully completed.</info> ';
            } else {
                $msg = '<error>Failed! API generation was interrupted.</error> ';
            }

            $this->output->writeln(['', $msg]);
        }
    }
}

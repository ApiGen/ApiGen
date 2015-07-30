<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Console\Helper;

use ApiGen\Contracts\Console\Helper\ProgressBarInterface;
use ApiGen\Contracts\Console\IO\IOInterface;
use Symfony\Component\Console\Helper\ProgressBar as ProgressBarHelper;

class ProgressBar implements ProgressBarInterface
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


    /**
     * {@inheritdoc}
     */
    public function init($maximum = 1)
    {
        $this->bar = new ProgressBarHelper($this->consoleIO->getOutput(), $maximum);
        $this->bar->setFormat($this->getBarFormat());
        $this->bar->start();
    }


    /**
     * {@inheritdoc}
     */
    public function increment($increment = 1)
    {
        if ($this->bar === null) {
            return;
        }

        $this->bar->advance($increment);
        if ($this->bar->getProgress() === $this->bar->getMaxSteps()) {
            $this->consoleIO->getOutput()->writeln(' - Finished!');
        }
    }


    /**
     * @return string
     */
    private function getBarFormat()
    {
        if ($this->getDebugOption()) {
            return 'debug';

        } else {
            return '<comment>%percent:3s% %</comment>';
        }
    }


    /**
     * @return bool
     */
    private function getDebugOption()
    {
        if ($this->consoleIO->getInput() && $this->consoleIO->getInput()->hasOption('debug')) {
            return $this->consoleIO->getInput()->getOption('debug');

        } else {
            return false;
        }
    }
}

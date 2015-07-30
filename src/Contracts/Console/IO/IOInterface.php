<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Console\IO;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface IOInterface
{

    /**
     * @return InputInterface
     */
    public function getInput();


    /**
     * @return OutputInterface
     */
    public function getOutput();


    /**
     * @param string $message
     */
    public function writeln($message);


    /**
     * @param string $question
     * @param NULL|string $default
     * @return string
     */
    public function ask($question, $default = null);
}

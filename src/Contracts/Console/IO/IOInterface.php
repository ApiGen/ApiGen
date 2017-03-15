<?php declare(strict_types=1);

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

<?php

namespace ApiGen\Console\IO;

use ApiGen\Console\Question\ConfirmationQuestion;
use ApiGen\Contracts\Console\IO\IOInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IO implements IOInterface
{

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var HelperSet
     */
    private $helperSet;


    public function __construct(HelperSet $helperSet, InputInterface $input, OutputInterface $output)
    {
        $this->helperSet = $helperSet;
        $this->input = $input;
        $this->output = $output;
    }


    /**
     * {@inheritdoc}
     */
    public function getInput()
    {
        return $this->input;
    }


    /**
     * {@inheritdoc}
     */
    public function getOutput()
    {
        return $this->output;
    }


    /**
     * {@inheritdoc}
     */
    public function writeln($message)
    {
        return $this->output->writeln($message);
    }


    /**
     * {@inheritdoc}
     */
    public function ask($question, $default = null)
    {
        if ($this->output->getVerbosity() === OutputInterface::VERBOSITY_QUIET) {
            return false;
        }

        /** @var QuestionHelper $helper */
        $helper = $this->helperSet->get('question');
        $question = new ConfirmationQuestion($question, $default);
        return $helper->ask($this->input, $this->output, $question);
    }
}

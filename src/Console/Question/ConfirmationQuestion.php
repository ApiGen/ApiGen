<?php

namespace ApiGen\Console\Question;

use Symfony\Component\Console\Question\ConfirmationQuestion as BaseConfirmationQuestion;

class ConfirmationQuestion extends BaseConfirmationQuestion
{

    /**
     * {@inheritdoc}
     */
    public function getQuestion()
    {
        return sprintf(
            '<info>%s</info> [<comment>%s</comment>] ',
            parent::getQuestion(),
            $this->getDefault() === true ? 'yes' : 'no'
        );
    }
}

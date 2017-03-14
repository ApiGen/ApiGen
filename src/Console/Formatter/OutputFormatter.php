<?php

namespace ApiGen\Console\Formatter;

use Symfony\Component\Console\Formatter\OutputFormatter as BaseOutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class OutputFormatter extends BaseOutputFormatter
{
    public function __construct()
    {
        parent::__construct(null, $this->getStyles());
    }


    /**
     * @return array|OutputFormatterStyle[]
     */
    private function getStyles()
    {
        return [
            'warning' => new OutputFormatterStyle('black', 'yellow')
        ];
    }
}

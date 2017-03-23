<?php declare(strict_types=1);

namespace ApiGen\Console\Formatter;

use Symfony\Component\Console\Formatter\OutputFormatter as BaseOutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

final class OutputFormatter extends BaseOutputFormatter
{
    public function __construct()
    {
        parent::__construct(null, $this->getStyles());
    }

    /**
     * @return OutputFormatterStyle[]
     */
    private function getStyles(): array
    {
        return [
            'warning' => new OutputFormatterStyle('black', 'yellow')
        ];
    }
}

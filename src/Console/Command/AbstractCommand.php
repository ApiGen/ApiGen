<?php declare(strict_types=1);

namespace ApiGen\Console\Command;

use Symfony\Component\Console\Command\Command;

abstract class AbstractCommand extends Command
{

    public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null)
    {
        $name = $this->dashFormat($name);
        return parent::addOption($name, $shortcut, $mode, $description, $default);
    }


    private function dashFormat(string $name): string
    {
        return preg_replace_callback('~([A-Z])~', function ($matches) {
            return '-' . strtolower($matches[1]);
        }, $name);
    }
}

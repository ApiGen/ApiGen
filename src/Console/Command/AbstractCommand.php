<?php declare(strict_types=1);

namespace ApiGen\Console\Command;

use Symfony\Component\Console\Command\Command;

abstract class AbstractCommand extends Command
{
    /**
     * @param string $name
     * @param ?string $shortcut
     * @param ?int $mode
     * @param string $description
     * @param ?mixed $default
     */
    public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null): Command
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

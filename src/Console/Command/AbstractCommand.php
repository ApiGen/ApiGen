<?php

namespace ApiGen\Console\Command;

use Symfony\Component\Console\Command\Command;

abstract class AbstractCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null)
    {
        $name = $this->dashFormat($name);
        return parent::addOption($name, $shortcut, $mode, $description, $default);
    }


    /**
     * @param string $name
     * @return string
     */
    private function dashFormat($name)
    {
        return preg_replace_callback('~([A-Z])~', function ($matches) {
            return '-' . strtolower($matches[1]);
        }, $name);
    }
}

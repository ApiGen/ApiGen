<?php

namespace ApiGen\Utils\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

class UtilsExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/services.neon')['services']
        );
    }
}

<?php

namespace ApiGen\Utils\DI;

use Nette\DI\CompilerExtension;

class UtilsExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $services = $this->loadFromFile(__DIR__ . '/services.neon');
        $this->compiler->parseServices($builder, $services);
    }
}

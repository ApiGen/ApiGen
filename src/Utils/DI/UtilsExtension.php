<?php declare(strict_types=1);

namespace ApiGen\Utils\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

final class UtilsExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/services.neon')['services']
        );
    }
}

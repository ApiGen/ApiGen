<?php declare(strict_types=1);

namespace ApiGen\Console\DI;

use ApiGen\Console\Application;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Command\Command;

class ConsoleExtension extends CompilerExtension
{

    // @todo: use external package
    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/services.neon')['services']
        );
    }


    public function beforeCompile(): void
    {
        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->prepareClassList();

        $application = $containerBuilder->getDefinitionByType(Application::class);
        foreach ($containerBuilder->findByType(Command::class) as $definition) {
            $application->addSetup('add', ['@' . $definition->getClass()]);
        }
    }
}

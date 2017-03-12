<?php

namespace ApiGen\Console\DI;

use ApiGen\Console\Application;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Command\Command;

class ConsoleExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/services.neon')['services']
        );
    }


    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();
        $builder->prepareClassList();

        $application = $builder->getDefinition($builder->getByType(Application::class));
        foreach ($builder->findByType(Command::class) as $definition) {
            $application->addSetup('add', ['@' . $definition->getClass()]);
        }
    }
}

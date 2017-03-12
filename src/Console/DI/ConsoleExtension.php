<?php

namespace ApiGen\Console\DI;

use ApiGen\Console\Application;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Command\Command;

class ConsoleExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $services = $this->loadFromFile(__DIR__ . '/services.neon');
        $this->compiler->parseServices($builder, $services);
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

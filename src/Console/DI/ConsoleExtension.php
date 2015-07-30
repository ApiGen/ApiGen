<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Console\DI;

use ApiGen\Console\Application;
use ApiGen\Console\Command\SelfUpdateCommand;
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
            if (! $this->isPhar() && $definition->getClass() === SelfUpdateCommand::class) {
                continue;
            }
            $application->addSetup('add', ['@' . $definition->getClass()]);
        }
    }


    /**
     * @return bool
     */
    private function isPhar()
    {
        return substr(__FILE__, 0, 5) === 'phar:';
    }
}

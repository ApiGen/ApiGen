<?php

namespace ApiGen\EventDispatcher\DI;

use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\Contracts\EventDispatcher\EventSubscriberInterface;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

class EventDispatcherExtension extends CompilerExtension
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

        $eventDispatcherDefinition = $builder->getDefinition($builder->getByType(EventDispatcherInterface::class));

        foreach ($builder->findByType(EventSubscriberInterface::class) as $subscriber) {
            $eventDispatcherDefinition->addSetup('addSubscriber', ['@' . $subscriber->getClass()]);
        }
    }
}

<?php

namespace ApiGen\EventDispatcher\DI;

use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\Contracts\EventDispatcher\EventSubscriberInterface;
use Nette\DI\CompilerExtension;

class EventDispatcherExtension extends CompilerExtension
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

        $eventDispatcherDefinition = $builder->getDefinition($builder->getByType(EventDispatcherInterface::class));

        foreach ($builder->findByType(EventSubscriberInterface::class) as $subscriber) {
            $eventDispatcherDefinition->addSetup('addSubscriber', ['@' . $subscriber->getClass()]);
        }
    }
}

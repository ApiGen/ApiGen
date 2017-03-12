<?php

namespace ApiGen\EventDispatcher\Tests\DI;

use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\EventDispatcher\DI\EventDispatcherExtension;
use ApiGen\EventDispatcher\SymfonyEventDispatcher;
use ApiGen\EventDispatcher\Tests\DI\EventDispatcherExtensionSource\SomeSubscriber;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;
use PHPUnit\Framework\TestCase;

class EventDispatcherExtensionTest extends TestCase
{

    public function testLoadConfiguration()
    {
        $consoleExtension = $this->createExtension();
        $consoleExtension->loadConfiguration();

        $builder = $consoleExtension->getContainerBuilder();
        $builder->prepareClassList();

        /** @var ServiceDefinition $eventDispatcherDefinition*/
        $eventDispatcherDefinition = $builder->getDefinition($builder->getByType(EventDispatcherInterface::class));
        $this->assertSame(SymfonyEventDispatcher::class, $eventDispatcherDefinition->getClass());
    }


    public function testLoadSubscribers()
    {
        $consoleExtension = $this->createExtension();
        $consoleExtension->loadConfiguration();

        $builder = $consoleExtension->getContainerBuilder();
        $builder->addDefinition('subscriber')
            ->setClass(SomeSubscriber::class);

        $consoleExtension->beforeCompile();

        $eventDispatcherDefinition = $builder->getDefinition($builder->getByType(EventDispatcherInterface::class));

        $this->assertCount(1, $eventDispatcherDefinition->getSetup());
        $this->assertSame('addSubscriber', $eventDispatcherDefinition->getSetup()[0]->getEntity());
        $this->assertSame(
            ['@' . SomeSubscriber::class],
            $eventDispatcherDefinition->getSetup()[0]->arguments
        );
    }


    /**
     * @return EventDispatcherExtension
     */
    private function createExtension()
    {
        $consoleExtension = new EventDispatcherExtension;
        $consoleExtension->setCompiler(new Compiler(new ContainerBuilder), 'compiler');
        return $consoleExtension;
    }
}

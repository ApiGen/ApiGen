<?php

namespace ApiGen\Tests\Console;

use ApiGen\ApiGen;
use ApiGen\Console\Application;
use ApiGen\Contracts\Console\Input\DefaultInputDefinitionFactoryInterface;
use ApiGen\Contracts\Console\IO\IOInterface;
use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\MemoryLimit;
use ApiGen\Tests\MethodInvoker;
use Kdyby\Events\EventManager;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApplicationTest extends TestCase
{

    /**
     * @var Application
     */
    private $application;


    protected function setUp()
    {
        $ioMock = Mockery::mock(IOInterface::class);
        $defaultInputDefinitionFactoryMock = Mockery::mock(DefaultInputDefinitionFactoryInterface::class);
        $defaultInputDefinitionFactoryMock->shouldReceive('create')->andReturn(new InputDefinition);
        $this->application = new Application(new ApiGen, new MemoryLimit, $ioMock, $defaultInputDefinitionFactoryMock);
    }


    public function testGetLongVersion()
    {
        $this->assertSame(
            '<info>ApiGen</info> version <comment>' . ApiGen::VERSION . '</comment>',
            $this->application->getLongVersion()
        );
    }
}

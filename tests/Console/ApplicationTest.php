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
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApplicationTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Application
     */
    private $application;

    /**
     * @var string
     */
    private $symfonyVersion = '2';

    protected function setUp()
    {
        $ioMock = Mockery::mock(IOInterface::class);
        $defaultInputDefinitionFactoryMock = Mockery::mock(DefaultInputDefinitionFactoryInterface::class);
        $defaultInputDefinitionFactoryMock->shouldReceive('create')->andReturn(new InputDefinition);
        $this->application = new Application(new ApiGen, new MemoryLimit, $ioMock, $defaultInputDefinitionFactoryMock);
        $this->symfonyVersion = (method_exists($this->application, 'asText')
            ? '2'
            : '3');
    }


    public function testGetLongVersion()
    {
        $longVersion = '<info>ApiGen</info> version <comment>' . ApiGen::VERSION . '</comment>';

        if ($this->symfonyVersion > 2) {
            $longVersion = 'ApiGen <info>' . ApiGen::VERSION . '</info>';
        }

        $this->assertSame(
            $longVersion,
            $this->application->getLongVersion()
        );
    }
}

<?php

namespace ApiGen\Tests\Console\Command;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\Contracts\Console\Helper\ProgressBarInterface;
use ApiGen\Contracts\Console\IO\IOInterface;
use ApiGen\Tests\ContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use ReflectionObject;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommandExecuteTest extends ContainerAwareTestCase
{

    /**
     * @var GenerateCommand
     */
    private $generateCommand;


    protected function setUp()
    {
        $this->generateCommand = $this->container->getByType(GenerateCommand::class);
    }


    public function testExecute()
    {
        $this->assertFileNotExists(TEMP_DIR . '/Api/index.html');

        $inputMock = Mockery::mock(InputInterface::class);
        $inputMock->shouldReceive('getOptions')->andReturn([
            'config' => null,
            'destination' => TEMP_DIR . '/Api',
            'source' => __DIR__ . '/Source'
        ]);
        $outputMock = Mockery::mock(OutputInterface::class);

        $io = $this->container->getByType(IOInterface::class);
        $reflection = new ReflectionObject($io);
        $output = $reflection->getProperty('output');
        $output->setAccessible(true);
        $output->setValue($io, new NullOutput);

        $this->assertSame(
            0, // success
            MethodInvoker::callMethodOnObject(
                $this->generateCommand,
                'execute',
                [$inputMock, $outputMock]
            )
        );

        $this->assertFileExists(TEMP_DIR . '/Api/index.html');
    }


    public function testExecuteWithError()
    {
        $inputMock = Mockery::mock(InputInterface::class);
        $outputMock = Mockery::mock(OutputInterface::class);
        $outputMock->shouldReceive('writeln');

        $this->assertSame(
            1, // failure
            MethodInvoker::callMethodOnObject(
                $this->generateCommand,
                'execute',
                [$inputMock, $outputMock]
            )
        );
    }
}

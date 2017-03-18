<?php declare(strict_types=1);

namespace ApiGen\Tests\Console\Command;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\Contracts\Console\IO\IOInterface;
use ApiGen\Tests\ContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use ReflectionObject;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateCommandExecuteTest extends ContainerAwareTestCase
{

    /**
     * @var GenerateCommand
     */
    private $generateCommand;


    protected function setUp(): void
    {
        $this->generateCommand = $this->container->getByType(GenerateCommand::class);
    }


    public function testExecute(): void
    {
        $this->assertFileNotExists(TEMP_DIR . '/Api/index.html');

        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->method('getOptions')->willReturn([
            'config' => null,
            'destination' => TEMP_DIR . '/Api',
            'source' => __DIR__ . '/Source'
        ]);
        $outputMock = $this->createMock(OutputInterface::class);

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


//    public function testExecuteWithError()
//    {
//        $inputMock = $this->createMock(InputInterface::class);
//        $inputMock->method('getOptions')->willReturn([]);
//
//        $this->assertSame(
//            1, // failure
//            MethodInvoker::callMethodOnObject(
//                $this->generateCommand,
//                'execute',
//                [$inputMock, new NullOutput()]
//            )
//        );
//
//        $this->assertFileNotExists(TEMP_DIR . '/Api/index.html');
//    }
}

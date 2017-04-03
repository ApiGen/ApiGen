<?php declare(strict_types=1);

namespace ApiGen\Tests\Console\Command;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateCommandExecuteTest extends AbstractContainerAwareTestCase
{
    /**
     * @var GenerateCommand
     */
    private $generateCommand;

    protected function setUp(): void
    {
        $this->generateCommand = $this->container->getByType(GenerateCommand::class);

        /** @var ConsoleOutput $output */
        $output = $this->container->getByType(OutputInterface::class);
        $output->setVerbosity(Output::VERBOSITY_QUIET);
    }

    public function testExecute(): void
    {
        $this->assertFileNotExists(TEMP_DIR . '/api/index.html');

        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->method('getArgument')->willReturn([
            __DIR__ . '/Source'
        ]);
        $inputMock->method('getOptions')->willReturn([
            'config' => null,
            'destination' => TEMP_DIR . '/api',
            'source' => __DIR__ . '/Source'
        ]);
        $outputMock = $this->createMock(OutputInterface::class);

        $this->assertSame(
            0, // success
            MethodInvoker::callMethodOnObject(
                $this->generateCommand,
                'execute',
                [$inputMock, $outputMock]
            )
        );

        $this->assertFileExists(TEMP_DIR . '/api/index.html');
    }

    /**
     * @expectedException \ApiGen\Configuration\Exceptions\ConfigurationException
     */
    public function testExecuteWithError(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->method('getArgument')->willReturn([
            __DIR__
        ]);
        $inputMock->method('getOptions')->willReturn([
            'config' => null
        ]);

        MethodInvoker::callMethodOnObject(
            $this->generateCommand,
            'execute',
            [$inputMock, new NullOutput()]
        );
    }
}

<?php declare(strict_types=1);

namespace ApiGen\Tests\Console\Command;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\ModularConfiguration\Exception\ConfigurationException;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\Output;

final class GenerateCommandExecuteTest extends AbstractContainerAwareTestCase
{
    /**
     * @var GenerateCommand
     */
    private $generateCommand;

    protected function setUp(): void
    {
        $this->generateCommand = $this->container->get(GenerateCommand::class);

        $output = $this->container->get(ConsoleOutput::class);
        $output->setVerbosity(Output::VERBOSITY_QUIET);
    }

    public function testExecute(): void
    {
        $this->assertFileNotExists(TEMP_DIR . '/api/index.html');

        $input = new ArrayInput([
            SourceOption::NAME => [__DIR__ . '/Source'],
            '--' . DestinationOption::NAME => TEMP_DIR . '/Api'
        ]);

        $exitCode = $this->generateCommand->run($input, new NullOutput);
        $this->assertSame(
            0, // success
            $exitCode
        );

        $this->assertFileExists(TEMP_DIR . '/Api/index.html');
    }

    public function testExecuteWithError(): void
    {
        $input = new ArrayInput([
            SourceOption::NAME => ['missing'],
            '--' . DestinationOption::NAME => TEMP_DIR,
        ]);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Source "missing" does not exist');

        $this->generateCommand->run($input, new NullOutput);
    }
}

<?php declare(strict_types=1);

namespace ApiGen\Tests\Console\Command;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\ModularConfiguration\Option\ConfigurationFileOption;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Symfony\Component\Console\Input\ArrayInput;
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

        $input = new ArrayInput([
            SourceOption::NAME => [__DIR__ . '/Source'],
            '--' . DestinationOption::NAME => TEMP_DIR . '/Api',
            '--' . ConfigurationFileOption::NAME => '...',
        ]);

        $exitCode = $this->generateCommand->run($input, new NullOutput);
        $this->assertSame(
            0, // success
            $exitCode
        );

        $this->assertFileExists(TEMP_DIR . '/Api/index.html');
    }

    /**
     * @expectedException \ApiGen\Configuration\Exceptions\ConfigurationException
     * @expectedExceptionMessage Source "missing" does not exist
     */
    public function testExecuteWithError(): void
    {
        $input = new ArrayInput([
            SourceOption::NAME => ['missing'],
            '--' . DestinationOption::NAME => TEMP_DIR,
            '--' . ConfigurationFileOption::NAME => 'wrong'
        ]);

        $this->generateCommand->run($input, new NullOutput);
    }
}

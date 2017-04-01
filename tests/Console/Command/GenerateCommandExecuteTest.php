<?php declare(strict_types=1);

namespace ApiGen\Tests\Console\Command;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Console\Command\GenerateCommand;
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
            ConfigurationOptions::SOURCE => [__DIR__ . '/Source'],
            '--' . ConfigurationOptions::DESTINATION => TEMP_DIR . '/Api',
        ]);

        $exitCode = $this->generateCommand->run($input, new NullOutput);
        $this->assertSame(
            0, // success
            $exitCode
        );

        $this->assertFileExists(TEMP_DIR . '/api/index.html');
    }

    /**
     * @expectedException \ApiGen\Configuration\Exceptions\ConfigurationException
     */
    public function testExecuteWithError(): void
    {
        $input = new ArrayInput([
            ConfigurationOptions::SOURCE => [__DIR__]
        ]);

        $this->generateCommand->run($input, new NullOutput);
    }
}

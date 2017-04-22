<?php declare(strict_types=1);

namespace ApiGen\Console\Command;

use ApiGen\Application\ApiGenApplication;
use ApiGen\Application\Command\RunCommand;
use ApiGen\Configuration\ConfigurationOptions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateCommand extends Command
{
    /**
     * @var string
     */
    private const NAME = 'generate';

    /**
     * @var ApiGenApplication
     */
    private $apiGenApplication;

    public function __construct(ApiGenApplication $apiGenApplication)
    {
        parent::__construct();

        $this->apiGenApplication = $apiGenApplication;
    }

    protected function configure(): void
    {
        $this->setName(self::NAME);
        $this->setDescription('Generate API documentation');
        $this->addArgument(
            ConfigurationOptions::SOURCE,
            InputArgument::IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Dirs or files documentation is generated for.'
        );
        // @todo: use ConfigurationDecorator
        $this->addOption(
            ConfigurationOptions::DESTINATION,
            null,
            InputOption::VALUE_REQUIRED,
            'Target dir for generated documentation.'
        );
        $this->addOption(
            ConfigurationOptions::CONFIG,
            null,
            InputOption::VALUE_REQUIRED,
            'Path to apigen.neon config file.',
            getcwd() . DIRECTORY_SEPARATOR . 'apigen.neon'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $runCommand = RunCommand::createFromInput($input);

        $this->apiGenApplication->runCommand($runCommand);

        return 0;
    }
}

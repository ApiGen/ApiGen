<?php declare(strict_types=1);

namespace ApiGen\Console\Command;

use ApiGen\Application\ApiGenApplication;
use ApiGen\Application\Command\RunCommand;
use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\ModularConfiguration\Contract\ConfigurationDecoratorInterface;
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

    /**
     * @var ConfigurationDecoratorInterface
     */
    private $configurationDecorator;

    public function __construct(
        ApiGenApplication $apiGenApplication,
        ConfigurationDecoratorInterface $configurationDecorator
    ) {
        $this->apiGenApplication = $apiGenApplication;
        $this->configurationDecorator = $configurationDecorator;

        parent::__construct();
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

        $this->configurationDecorator->decorateCommand($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $runCommand = RunCommand::createFromInput($input);

        $this->apiGenApplication->runCommand($runCommand);

        return 0;
    }
}

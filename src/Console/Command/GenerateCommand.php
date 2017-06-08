<?php declare(strict_types=1);

namespace ApiGen\Console\Command;

use ApiGen\Application\ApiGenApplication;
use ApiGen\Application\Command\RunCommand;
use ApiGen\ModularConfiguration\CommandDecorator;
use ApiGen\ModularConfiguration\ConfigurationResolver;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
     * @var CommandDecorator
     */
    private $commandDecorator;

    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    public function __construct(
        ApiGenApplication $apiGenApplication,
        CommandDecorator $commandDecorator,
        ConfigurationResolver $configurationResolver
    ) {
        $this->apiGenApplication = $apiGenApplication;
        $this->commandDecorator = $commandDecorator;
        $this->configurationResolver = $configurationResolver;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(self::NAME);
        $this->setDescription('Generate API documentation');
        $this->commandDecorator->decorateCommand($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->ensureDestinationIsSet($input);

        $runCommand = RunCommand::createFromInput($input);

        $this->apiGenApplication->runCommand($runCommand);

        return 0;
    }

    private function ensureDestinationIsSet(InputInterface $input): void
    {
        $this->configurationResolver->resolveValue(DestinationOption::NAME, $input->getOption(DestinationOption::NAME));
    }
}

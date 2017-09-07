<?php declare(strict_types=1);

namespace ApiGen\Application;

use ApiGen\Application\Command\RunCommand;
use ApiGen\Configuration\Configuration;
use ApiGen\Generator\GeneratorQueue;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\OverwriteOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Utils\FileSystem;
use Symfony\Component\Console\Output\OutputInterface;

final class ApiGenApplication
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var GeneratorQueue
     */
    private $generatorQueue;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(
        Configuration $configuration,
        Parser $parser,
        GeneratorQueue $generatorQueue,
        FileSystem $fileSystem,
        OutputInterface $output
    ) {
        $this->configuration = $configuration;
        $this->parser = $parser;
        $this->generatorQueue = $generatorQueue;
        $this->fileSystem = $fileSystem;
        $this->output = $output;
    }

    public function runCommand(RunCommand $runCommand): void
    {
        $options = $this->configuration->resolveOptions([
            SourceOption::NAME => $runCommand->getSource(),
            DestinationOption::NAME => $runCommand->getDestination(),
        ]);

        $this->output->write('Parsing reflections (this may take a while)...');
        $this->parser->parseFilesAndDirectories($options[SourceOption::NAME]);
        $this->output->writeln(' <info>done!</info>');
        $this->output->writeln('Generating documentation...');

        $this->prepareDestination($options[DestinationOption::NAME], (bool) $options[OverwriteOption::NAME]);
        $this->generatorQueue->run();

        $this->output->writeln('<info>Your documentation has been generated successfully!</info>');
    }

    private function prepareDestination(string $destination, bool $shouldOverwrite): void
    {
        if ($shouldOverwrite) {
            $this->fileSystem->purgeDir($destination);
        }

        $this->copyThemeResourcesToDestination($destination);
    }

    private function copyThemeResourcesToDestination(string $destination): void
    {
        $this->fileSystem->copyDirectory(
            $this->configuration->getTemplatesDirectory() . '/resources',
            $destination . '/resources'
        );
    }
}

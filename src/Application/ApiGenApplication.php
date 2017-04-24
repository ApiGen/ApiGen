<?php declare(strict_types=1);

namespace ApiGen\Application;

use ApiGen\Application\Command\RunCommand;
use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\ModularConfiguration\Option\ConfigurationFileOption;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\Theme\ThemeResources;
use ApiGen\Utils\FileSystem;
use ApiGen\Utils\Finder\FinderInterface;

final class ApiGenApplication
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var GeneratorQueueInterface
     */
    private $generatorQueue;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    /**
     * @var ThemeResources
     */
    private $themeResources;

    /**
     * @var FinderInterface
     */
    private $finder;

    public function __construct(
        ConfigurationInterface $configuration,
        ParserInterface $parser,
        GeneratorQueueInterface $generatorQueue,
        FileSystem $fileSystem,
        ThemeResources $themeResources,
        FinderInterface $finder
    ) {
        $this->configuration = $configuration;
        $this->parser = $parser;
        $this->generatorQueue = $generatorQueue;
        $this->fileSystem = $fileSystem;
        $this->themeResources = $themeResources;
        $this->finder = $finder;
    }

    public function runCommand(RunCommand $runCommand): void
    {
        $options = $this->configuration->prepareOptions([
            ConfigurationOptions::SOURCE => $runCommand->getSource(),
            DestinationOption::NAME => $runCommand->getDestination(),
            ConfigurationFileOption::NAME => $runCommand->getConfigPath()
        ]);

        $this->scanAndParse($options);
        $this->generate($options);
    }

    /**
     * @param mixed[] $options
     */
    private function scanAndParse(array $options): void
    {
        $files = $this->finder->find(
            $options[ConfigurationOptions::SOURCE],
            $options[ConfigurationOptions::EXCLUDE],
            $options[ConfigurationOptions::EXTENSIONS]
        );

        $this->parser->parseFiles($files);
    }

    /**
     * @param mixed[] $options
     */
    private function generate(array $options): void
    {
        $this->prepareDestination(
            $options[DestinationOption::NAME],
            (bool) $options[ConfigurationOptions::FORCE_OVERWRITE]
        );
        $this->generatorQueue->run();
    }

    private function prepareDestination(string $destination, bool $shouldOverwrite): void
    {
        if ($shouldOverwrite) {
            $this->fileSystem->purgeDir($destination);
        }

        $this->themeResources->copyToDestination($destination);
    }
}

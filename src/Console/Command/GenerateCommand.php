<?php declare(strict_types=1);

namespace ApiGen\Console\Command;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Theme\ThemeResources;
use ApiGen\Utils\FileSystem;
use ApiGen\Utils\Finder\FinderInterface;
use Nette\DI\Config\Loader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateCommand extends AbstractCommand
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;

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

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(
        Configuration $configuration,
        ParserInterface $parser,
        ParserStorageInterface $parserStorage,
        GeneratorQueueInterface $generatorQueue,
        FileSystem $fileSystem,
        ThemeResources $themeResources,
        FinderInterface $finder
    ) {
        parent::__construct();

        $this->configuration = $configuration;
        $this->parser = $parser;
        $this->parserStorage = $parserStorage;
        $this->generatorQueue = $generatorQueue;
        $this->fileSystem = $fileSystem;
        $this->themeResources = $themeResources;
        $this->finder = $finder;
    }

    protected function configure(): void
    {
        $this->setName('generate');
        $this->setDescription('Generate API documentation');
        $this->addArgument(
            ConfigurationOptions::SOURCE,
            InputArgument::IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Dirs or files documentation is generated for.'
        );
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
        $this->output = $output;

        $cliOptions = [
            ConfigurationOptions::SOURCE => $input->getArgument(ConfigurationOptions::SOURCE),
        ] + $input->getOptions();

        $options = $this->prepareOptions($cliOptions);
        $this->scanAndParse($options);
        $this->generate($options);
        return 0;
    }

    /**
     * @param mixed[] $options
     */
    private function scanAndParse(array $options): void
    {
        $this->output->writeln('<info>Scanning sources and parsing</info>');

        $files = $this->finder->find(
            $options[ConfigurationOptions::SOURCE],
            $options[ConfigurationOptions::EXCLUDE],
            $options[ConfigurationOptions::EXTENSIONS]
        );
        $this->parser->parse($files);
    }

    /**
     * @param mixed[] $options
     */
    private function generate(array $options): void
    {
        $this->prepareDestination(
            $options[ConfigurationOptions::DESTINATION],
            (bool) $options[ConfigurationOptions::FORCE_OVERWRITE]
        );
        $this->output->writeln('<info>Generating API documentation</info>');
        $this->generatorQueue->run();
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    private function prepareOptions(array $options): array
    {
        $options = $this->loadOptionsFromConfig($options);

        return $this->configuration->resolveOptions($options);
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    private function loadOptionsFromConfig(array $options): array
    {
        $configFile = $options[ConfigurationOptions::CONFIG] ?? getcwd() . DIRECTORY_SEPARATOR . 'apigen.neon';
        $configFile = $this->fileSystem->getAbsolutePath($configFile);

        if (file_exists($configFile)) {
            $configFileOptions = (new Loader())->load($configFile);
            return array_merge($options, $configFileOptions);
        }

        return $options;
    }

    private function prepareDestination(string $destination, bool $shouldOverwrite = false): void
    {
        if ($shouldOverwrite) {
            $this->fileSystem->purgeDir($destination);
        }

        $this->themeResources->copyToDestination($destination);
    }
}

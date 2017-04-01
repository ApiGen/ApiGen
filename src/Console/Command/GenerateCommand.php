<?php declare(strict_types=1);

namespace ApiGen\Console\Command;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Theme\ThemeResources;
use ApiGen\Utils\FileSystem;
use ApiGen\Utils\Finder\FinderInterface;
use Nette\DI\Config\Loader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateCommand extends Command
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

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(
        ConfigurationInterface $configuration,
        ParserInterface $parser,
        GeneratorQueueInterface $generatorQueue,
        FileSystem $fileSystem,
        ThemeResources $themeResources,
        FinderInterface $finder
    ) {
        parent::__construct();

        $this->configuration = $configuration;
        $this->parser = $parser;
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

        $this->parser->parseFiles($files);
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

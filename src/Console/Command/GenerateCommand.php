<?php declare(strict_types=1);

namespace ApiGen\Console\Command;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Console\IO\IOInterface;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Theme\ThemeResources;
use ApiGen\Utils\FileSystem;
use ApiGen\Utils\Finder\FinderInterface;
use Nette\DI\Config\Loader;
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
     * @var IOInterface
     */
    private $io;

    /**
     * @var FinderInterface
     */
    private $finder;

    public function __construct(
        Configuration $configuration,
        ParserInterface $parser,
        ParserStorageInterface $parserStorage,
        GeneratorQueueInterface $generatorQueue,
        FileSystem $fileSystem,
        ThemeResources $themeResources,
        IOInterface $io,
        FinderInterface $finder
    ) {
        parent::__construct();

        $this->configuration = $configuration;
        $this->parser = $parser;
        $this->parserStorage = $parserStorage;
        $this->generatorQueue = $generatorQueue;
        $this->fileSystem = $fileSystem;
        $this->themeResources = $themeResources;
        $this->io = $io;
        $this->finder = $finder;
    }

    protected function configure(): void
    {
        $this->setName('generate');
        $this->setDescription('Generate API documentation');

        $this->addOption(
                ConfigurationOptions::SOURCE,
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Dirs or files documentation is generated for.'
            )
            ->addOption(
                ConfigurationOptions::DESTINATION,
                null,
                InputOption::VALUE_REQUIRED,
                'Target dir for documentation.'
            )
            ->addOption(
                ConfigurationOptions::ACCESS_LEVELS,
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Access levels of included method and properties [options: public, protected, private].',
                ['public', 'protected']
            )
            ->addOption(
                ConfigurationOptions::ANNOTATION_GROUPS,
                null,
                InputOption::VALUE_REQUIRED,
                'Generate page with elements with specific annotation.'
            )
            ->addOption(
                ConfigurationOptions::CONFIG,
                null,
                InputOption::VALUE_REQUIRED,
                'Custom path to apigen.neon config file.',
                getcwd() . '/apigen.neon'
            )
            ->addOption(
                ConfigurationOptions::GOOGLE_CSE_ID,
                null,
                InputOption::VALUE_REQUIRED,
                'Custom google search engine id (for search box).'
            )
            ->addOption(
                ConfigurationOptions::BASE_URL,
                null,
                InputOption::VALUE_REQUIRED,
                'Base url used for sitemap (for search box).'
            )
            ->addOption('googleAnalytics', null, InputOption::VALUE_REQUIRED, 'Google Analytics tracking code.')
            ->addOption(
                ConfigurationOptions::EXTENSIONS,
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Scanned file extensions.',
                ['php']
            )
            ->addOption(
                ConfigurationOptions::EXCLUDE,
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Directories and files matching this mask will not be parsed (e.g. */tests/*).'
            )
            ->addOption(
                ConfigurationOptions::MAIN,
                null,
                InputOption::VALUE_REQUIRED,
                'Elements with this name prefix will be first in tree.'
            )
            ->addOption(
                ConfigurationOptions::TEMPLATE_CONFIG,
                null,
                InputOption::VALUE_REQUIRED,
                'Your own template config, has higher priority than --template-theme.',
                getcwd() . '/packages/ThemeDefault/src/config.neon'
            )
            ->addOption(
                ConfigurationOptions::TITLE,
                null,
                InputOption::VALUE_REQUIRED,
                'Title of generated documentation.'
            )
            ->addOption(
                ConfigurationOptions::FORCE_OVERWRITE,
                'o',
                InputOption::VALUE_NONE,
                'Force overwrite destination directory'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = $this->prepareOptions($input->getOptions());
        $this->scanAndParse($options);
        $this->generate($options);
        return 0;
    }

    /**
     * @param mixed[] $options
     */
    private function scanAndParse(array $options): void
    {
        $this->io->writeln('<info>Scanning sources and parsing</info>');

        $files = $this->finder->find(
            $options[ConfigurationOptions::SOURCE],
            $options[ConfigurationOptions::EXCLUDE],
            $options[ConfigurationOptions::EXTENSIONS]
        );
        $this->parser->parse($files);

        $stats = $this->parserStorage->getDocumentedStats();
        $this->io->writeln(sprintf(
            'Found <comment>%d classes</comment>, <comment>%d constants</comment> and <comment>%d functions</comment>',
            $stats['classes'],
            $stats['constants'],
            $stats['functions']
        ));
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
        $this->io->writeln('<info>Generating API documentation</info>');
        $this->generatorQueue->run();
    }

    /**
     * @param mixed[] $cliOptions
     * @return mixed[]
     */
    private function prepareOptions(array $cliOptions): array
    {
        $options = $this->convertDashKeysToCamel($cliOptions);
        $options = $this->loadOptionsFromConfig($options);

        return $this->configuration->resolveOptions($options);
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    private function convertDashKeysToCamel(array $options): array
    {
        foreach ($options as $key => $value) {
            $camelKey = $this->camelFormat($key);
            if ($key !== $camelKey) {
                $options[$camelKey] = $value;
                unset($options[$key]);
            }
        }

        return $options;
    }

    private function camelFormat(string $name): string
    {
        return preg_replace_callback('~-([a-z])~', function ($matches) {
            return strtoupper($matches[1]);
        }, $name);
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    private function loadOptionsFromConfig(array $options): array
    {
        $configFilePaths = $this->getPossiblePathsForConfig($options);

        foreach ($configFilePaths as $configFile) {
            if (file_exists($configFile)) {
                $configFileOptions = (new Loader())->load($configFile);
                return array_merge($options, $configFileOptions);
            }
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

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    private function getPossiblePathsForConfig(array $options): array
    {
        $filePaths = [];

        if ($options[ConfigurationOptions::CONFIG]) {
            $filePaths[] = $options[ConfigurationOptions::CONFIG];
        }

        $filePaths[] = getcwd() . '/apigen.neon';

        return $filePaths;
    }
}

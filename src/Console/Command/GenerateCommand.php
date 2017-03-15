<?php declare(strict_types=1);

namespace ApiGen\Console\Command;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\Readers\ReaderFactory;
use ApiGen\Contracts\Console\IO\IOInterface;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Theme\ThemeResources;
use ApiGen\Utils\FileSystem;
use ApiGen\Utils\Finder\FinderInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TokenReflection\Exception\FileProcessingException;

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
    private $parserResult;

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
        ParserStorageInterface $parserResult,
        GeneratorQueueInterface $generatorQueue,
        FileSystem $fileSystem,
        ThemeResources $themeResources,
        IOInterface $io,
        FinderInterface $finder
    ) {
        parent::__construct();

        $this->configuration = $configuration;
        $this->parser = $parser;
        $this->parserResult = $parserResult;
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
                'source',
                's',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Dirs or files documentation is generated for.'
            )
            ->addOption('destination', 'd', InputOption::VALUE_REQUIRED, 'Target dir for documentation.')
            ->addOption(
                'accessLevels',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Access levels of included method and properties [options: public, protected, private].',
                ['public', 'protected']
            )
            ->addOption(
                'annotationGroups',
                null,
                InputOption::VALUE_REQUIRED,
                'Generate page with elements with specific annotation.'
            )
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED,
                'Custom path to apigen.neon config file.',
                getcwd() . '/apigen.neon'
            )
            ->addOption(
                'googleCseId',
                null,
                InputOption::VALUE_REQUIRED,
                'Custom google search engine id (for search box).'
            )
            ->addOption(
                'baseUrl',
                null,
                InputOption::VALUE_REQUIRED,
                'Base url used for sitemap (for search box).'
            )
            ->addOption('googleAnalytics', null, InputOption::VALUE_REQUIRED, 'Google Analytics tracking code.')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Turn on debug mode.')
            ->addOption(
                'extensions',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Scanned file extensions.',
                ['php']
            )
            ->addOption(
                'exclude',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Directories and files matching this mask will not be parsed (e.g. */tests/*).'
            )
            ->addOption(
                'main',
                null,
                InputOption::VALUE_REQUIRED,
                'Elements with this name prefix will be first in tree.'
            )
            ->addOption('internal', null, InputOption::VALUE_NONE, 'Include elements marked as @internal.')
            ->addOption('php', null, InputOption::VALUE_NONE, 'Generate documentation for PHP internal classes.')
            ->addOption(
                'noSourceCode',
                null,
                InputOption::VALUE_NONE,
                'Do not generate highlighted source code for elements.'
            )
            ->addOption('templateTheme', null, InputOption::VALUE_REQUIRED, 'ApiGen template theme name.', 'default')
            ->addOption(
                'templateConfig',
                null,
                InputOption::VALUE_REQUIRED,
                'Your own template config, has higher priority than --template-theme.'
            )
            ->addOption('title', null, InputOption::VALUE_REQUIRED, 'Title of generated documentation.')
            ->addOption(
                'overwrite',
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


    private function scanAndParse(array $options): void
    {
        $this->io->writeln('<info>Scanning sources and parsing</info>');

        $files = $this->finder->find($options['source'], $options['exclude'], $options['extensions']);
        $this->parser->parse($files);

        $this->reportParserErrors($this->parser->getErrors());

        $stats = $this->parserResult->getDocumentedStats();
        $this->io->writeln(sprintf(
            'Found <comment>%d classes</comment>, <comment>%d constants</comment> and <comment>%d functions</comment>',
            $stats['classes'],
            $stats['constants'],
            $stats['functions']
        ));
    }


    private function generate(array $options): void
    {
        $this->prepareDestination($options['destination'], $options['overwrite']);
        $this->io->writeln('<info>Generating API documentation</info>');
        $this->generatorQueue->run();
    }


    private function reportParserErrors(array $errors): void
    {
        /** @var FileProcessingException[] $errors */
        foreach ($errors as $error) {
            $output = null;
            if ($this->configuration->getOption('debug')) {
                $output = $error->getDetail();
            } else {
                /** @var \Exception[] $reasons */
                $reasons = $error->getReasons();
                if (isset($reasons[0]) && count($reasons)) {
                    $output = $reasons[0]->getMessage();
                }
            }
            if ($output) {
                $this->io->writeln(sprintf('<error>Parse error: "%s"</error>', $output));
            }
        }
    }


    private function prepareOptions(array $cliOptions): array
    {
        $options = $this->convertDashKeysToCamel($cliOptions);
        $options = $this->loadOptionsFromConfig($options);

        return $this->configuration->resolveOptions($options);
    }


    /**
     * @return array
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
     * @return array
     */
    private function loadOptionsFromConfig(array $options): array
    {
        $configFilePaths = [
            $options['config'],
            getcwd() . '/apigen.neon',
            getcwd() . '/apigen.yaml',
            getcwd() . '/apigen.neon.dist',
            getcwd() . '/apigen.yaml.dist'
        ];

        foreach ($configFilePaths as $configFile) {
            if (file_exists($configFile)) {
                $configFileOptions = ReaderFactory::getReader($configFile)->read();
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
}

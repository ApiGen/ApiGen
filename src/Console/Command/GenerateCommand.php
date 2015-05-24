<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Console\Command;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\Readers\ReaderFactory as ConfigurationReader;
use ApiGen\Contracts\Console\IO\IOInterface;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Scanner\Scanner;
use ApiGen\Theme\ThemeResources;
use ApiGen\Utils\FileSystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TokenReflection\Exception\FileProcessingException;


class GenerateCommand extends AbstractCommand
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
	 * @var Scanner
	 */
	private $scanner;

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


	public function __construct(
		Configuration $configuration,
		Scanner $scanner,
		ParserInterface $parser,
		ParserStorageInterface $parserResult,
		GeneratorQueueInterface $generatorQueue,
		FileSystem $fileSystem,
		ThemeResources $themeResources,
		IOInterface $io
	) {
		parent::__construct();
		$this->configuration = $configuration;
		$this->scanner = $scanner;
		$this->parser = $parser;
		$this->parserResult = $parserResult;
		$this->generatorQueue = $generatorQueue;
		$this->fileSystem = $fileSystem;
		$this->themeResources = $themeResources;
		$this->io = $io;
	}


	protected function configure()
	{
		$this->setName('generate')
			->setDescription('Generate API documentation')
			->addOption('source', 's', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
				'Dirs or files documentation is generated for.')
			->addOption('destination', 'd', InputOption::VALUE_REQUIRED, 'Target dir for documentation.')
			->addOption('accessLevels', NULL, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
				'Access levels of included method and properties [options: public, protected, private].',
				['public', 'protected'])
			->addOption('annotationGroups', NULL, InputOption::VALUE_REQUIRED,
				'Generate page with elements with specific annotation.')
			->addOption('config', NULL, InputOption::VALUE_REQUIRED,
				'Custom path to apigen.neon config file.', getcwd() . '/apigen.neon')
			->addOption('googleCseId', NULL, InputOption::VALUE_REQUIRED,
				'Custom google search engine id (for search box).')
			->addOption('baseUrl', NULL, InputOption::VALUE_REQUIRED,
				'Base url used for sitemap (for search box).')
			->addOption('googleAnalytics', NULL, InputOption::VALUE_REQUIRED, 'Google Analytics tracking code.')
			->addOption('debug', NULL, InputOption::VALUE_NONE, 'Turn on debug mode.')
			->addOption('download', NULL, InputOption::VALUE_NONE,
				'Add link to ZIP archive of documentation.')
			->addOption('extensions', NULL, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
				'Scanned file extensions.', ['php'])
			->addOption('exclude', NULL, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
				'Directories and files matching this mask will not be parsed (e.g. */tests/*).')
			->addOption('groups', NULL, InputOption::VALUE_REQUIRED,
				'The way elements are grouped in menu [options: namespaces, packages].', 'namespaces')
			->addOption('main', NULL, InputOption::VALUE_REQUIRED,
				'Elements with this name prefix will be first in tree.')
			->addOption('internal', NULL, InputOption::VALUE_NONE, 'Include elements marked as @internal.')
			->addOption('php', NULL, InputOption::VALUE_NONE, 'Generate documentation for PHP internal classes.')
			->addOption('noSourceCode', NULL, InputOption::VALUE_NONE,
				'Do not generate highlighted source code for elements.')
			->addOption('templateTheme', NULL, InputOption::VALUE_REQUIRED, 'ApiGen template theme name.', 'default')
			->addOption('templateConfig', NULL, InputOption::VALUE_REQUIRED,
				'Your own template config, has higher priority then --template-theme.')
			->addOption('title', NULL, InputOption::VALUE_REQUIRED, 'Title of generated documentation.')
			->addOption('tree', NULL, InputOption::VALUE_NONE,
				'Generate tree view of classes, interfaces, traits and exceptions.')

			/**
			 * @deprecated since version 4.2, to be removed in 5.0
			 */
			->addOption('deprecated', NULL, InputOption::VALUE_NONE,
				'Generate documentation for elements marked as @deprecated (deprecated, only present for BC).')
			->addOption('todo', NULL, InputOption::VALUE_NONE,
				'Generate documentation for elements marked as @todo (deprecated, only present for BC).')
			->addOption('charset', NULL, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
				'Charset of scanned files (deprecated, only present for BC).')
			->addOption('skip-doc-path', NULL, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
				'Files matching this mask will be included in class tree,'
				. ' but will not create a link to their documentation (deprecated, only present for BC).');
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {
			$options = $this->prepareOptions($input->getOptions());
			$this->scanAndParse($options);
			$this->generate($options);
			return 0;

		} catch (\Exception $e) {
			$output->writeln(
				sprintf(PHP_EOL . '<error>%s</error>', $e->getMessage())
			);
			return 1;
		}
	}


	private function scanAndParse(array $options)
	{
		$this->io->writeln('<info>Scanning sources and parsing</info>');

		$files = $this->scanner->scan($options['source'], $options['exclude'], $options['extensions']);
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


	private function generate(array $options)
	{
		$this->prepareDestination($options['destination']);
		$this->io->writeln('<info>Generating API documentation</info>');
		$this->generatorQueue->run();
	}


	private function reportParserErrors(array $errors)
	{
		/** @var FileProcessingException[] $errors */
		foreach ($errors as $error) {
			/** @var \Exception[] $reasons */
			$reasons = $error->getReasons();
			if (count($reasons) && isset($reasons[0])) {
				$this->io->writeln(
					sprintf('<error>Parse error: "%s"</error>', $reasons[0]->getMessage())
				);
			}
		}
	}


	/**
	 * @return array
	 */
	private function prepareOptions(array $cliOptions)
	{
		$cliOptions = $this->convertDashKeysToCamel($cliOptions);
		$configFile = $cliOptions['config'];
		$options = $cliOptions;

		if (file_exists($configFile)) {
			// get reader by file extension
			$configFileOptions = ConfigurationReader::getReader($configFile)->read();
			$options = array_merge($options, $configFileOptions);
		}

		$this->warnAboutDeprecatedOptions($options);
		$options = $this->unsetDeprecatedOptions($options);

		return $this->configuration->resolveOptions($options);
	}


	/**
	 * @return array
	 */
	private function convertDashKeysToCamel(array $options)
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


	/**
	 * @param string $name
	 * @return string
	 */
	private function camelFormat($name)
	{
		return preg_replace_callback('~-([a-z])~', function ($matches) {
			return strtoupper($matches[1]);
		}, $name);
	}


	/**
	 * @param string $destination
	 */
	private function prepareDestination($destination)
	{
		$this->cleanDestinationWithCaution($destination);
		$this->themeResources->copyToDestination($destination);
	}


	/**
	 * @param string $destination
	 */
	private function cleanDestinationWithCaution($destination)
	{
		if ( ! $this->fileSystem->isDirEmpty($destination)) {
			if ($this->io->ask('<warning>Destination is not empty. Do you want to erase it?</warning>', TRUE)) {
				$this->fileSystem->purgeDir($destination);
			}
		}
	}


	/**
	 * @deprecated since version 4.2, to be removed in 5.0
	 */
	private function warnAboutDeprecatedOptions(array $options)
	{
		if (isset($options['charset']) && $options['charset']) {
			$this->io->writeln('<warning>You are using the deprecated option "charset". UTF-8 is default now.</warning>');
		}

		if (isset($options['deprecated']) && $options['deprecated']) {
			$this->io->writeln(
				'<warning>You are using the deprecated option "deprecated". ' .
				'Use "--annotation-groups=deprecated" instead</warning>'
			);
		}

		if (isset($options['todo']) && $options['todo']) {
			$this->io->writeln(
				'<warning>You are using the deprecated option "todo". Use "--annotation-groups=todo" instead</warning>'
			);
		}

		if (isset($options['skipDocPath']) && $options['skipDocPath']) {
			$this->io->writeln(
				'<warning>You are using the deprecated option "skipDocPath". Use "exclude" instead.</warning>'
			);
		}
	}


	/**
	 * @deprecated since version 4.2, to be removed in 5.0
	 *
	 * @return array
	 */
	private function unsetDeprecatedOptions(array $options)
	{
		unset($options['charset'], $options['skipDocPath']);
		return $options;
	}

}

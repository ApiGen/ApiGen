<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Command;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Configuration\ConfigurationOptionsResolver as COR;
use ApiGen\FileSystem\Wiper;
use ApiGen\Generator\Generator;
use ApiGen\Generator\GeneratorQueue;
use ApiGen\Neon\NeonFile;
use ApiGen\Parser\Parser;
use ApiGen\Scanner\Scanner;
use InvalidArgumentException;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


class GenerateCommand extends Command
{

	/**
	 * @var Generator
	 */
	private $generator;

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var Wiper
	 */
	private $wiper;

	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @var Scanner
	 */
	private $scanner;

	/**
	 * @var GeneratorQueue
	 */
	private $generatorQueue;


	public function __construct(
		Generator $generator,
		Wiper $wiper,
		Configuration $configuration,
		Scanner $scanner,
		Parser $parser,
		GeneratorQueue $generatorQueue
	) {
		parent::__construct();
		$this->generator = $generator;
		$this->wiper = $wiper;
		$this->configuration = $configuration;
		$this->scanner = $scanner;
		$this->parser = $parser;
		$this->generatorQueue = $generatorQueue;
	}


	protected function configure()
	{
		$this->setName('generate')
			->setDescription('Generate API documentation')
			->setDefinition([
				new InputOption(CO::DESTINATION, 'd', NULL,
					'Target dir for documentation.'),
				new InputOption(CO::SOURCE, 's', InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
					'Dirs documentation is generated for (separate multiple items with a space).', NULL),
				new InputOption(CO::AUTOCOMPLETE, NULL, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
					'Element supported by autocomplete in search input.',
					array(COR::AC_CLASSES, COR::AC_CONSTANTS, COR::AC_FUNCTIONS)),
				new InputOption(CO::ACCESS_LEVELS, NULL, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
					'Access levels of included method and properties', array(COR::AL_PUBLIC, COR::AL_PROTECTED)),
				new InputOption(CO::BASE_URL, NULL, InputOption::VALUE_OPTIONAL,
					'Base url used for sitemap (useful for public doc.'),
				new InputOption(CO::CONFIG, NULL, InputOption::VALUE_OPTIONAL,
					'Custom path to apigen.neon config file.', getcwd() . '/apigen.neon'),
				new InputOption(CO::GOOGLE_CSE_ID, NULL, InputOption::VALUE_OPTIONAL,
					'Custom google search engine id (for search box).'),
				new InputOption(CO::GOOGLE_ANALYTICS, NULL, InputOption::VALUE_OPTIONAL,
					'Google Analytics tracking code..'),
				new InputOption(CO::DEBUG, NULL, InputOption::VALUE_OPTIONAL,
					'Turn on debug mode.', FALSE),
				new InputOption(CO::DEPRECATED, NULL, InputOption::VALUE_OPTIONAL,
					'Generate documentation for elements marked as @deprecated.', FALSE),
				new InputOption(CO::DOWNLOAD, NULL, InputOption::VALUE_OPTIONAL,
					'Add link to ZIP archive of documentation.', FALSE),
				new InputOption(CO::EXTENSIONS, NULL, InputOption::VALUE_IS_ARRAY | InputArgument::OPTIONAL,
					'Scanned file extensions.', array('php')),
				new InputOption(CO::EXCLUDE, NULL, InputOption::VALUE_IS_ARRAY | InputArgument::OPTIONAL,
					'Directories and files matching this mask will not be parsed.'),
				new InputOption(CO::GROUPS, NULL, InputOption::VALUE_OPTIONAL,
					'The way elements are grouped in menu.', 'auto'),
				new InputOption(CO::CHARSET, NULL, InputOption::VALUE_IS_ARRAY | InputArgument::OPTIONAL,
					'Charset of scanned files.'),
				new InputOption(CO::MAIN, NULL, InputOption::VALUE_OPTIONAL,
					'Elements with this name prefix will be first in tree.'),
				new InputOption(CO::INTERNAL, NULL, InputOption::VALUE_OPTIONAL,
					'Include elements marked as @internal.', FALSE),
				new InputOption(CO::PHP, NULL, InputOption::VALUE_OPTIONAL,
					'Generate documentation for PHP internal classes.', TRUE),
				new InputOption(CO::SKIP_DOC_PATH, NULL, InputOption::VALUE_IS_ARRAY | InputArgument::OPTIONAL,
					'Files matching this mask will be included in class tree,'
					. ' but will not create a link to their documentation.'),
				new InputOption(CO::SKIP_DOC_PREFIX, NULL, InputOption::VALUE_IS_ARRAY | InputArgument::OPTIONAL,
					'Files starting this name will be included in class tree,'
					. ' but will not create a link to their documentation.'),
				new InputOption(CO::SOURCE_CODE, NULL, InputOption::VALUE_REQUIRED,
					'Generate highlighted source code for elements.', TRUE),
				new InputOption(CO::TEMPLATE_THEME, NULL, InputOption::VALUE_OPTIONAL,
					'ApiGen template theme name.', 'default'),
				new InputOption(CO::TEMPLATE_CONFIG, NULL, InputOption::VALUE_OPTIONAL,
					'Your own template config, has higher priority ' . CO::TEMPLATE_THEME . '.'),
				new InputOption(CO::TITLE, NULL, InputOption::VALUE_OPTIONAL,
					'Title of generated documentation.'),
				new InputOption(CO::TODO, NULL, InputOption::VALUE_OPTIONAL,
					'Generate documentation for elements marked as @todo.', FALSE),
				new InputOption(CO::TREE, NULL, InputOption::VALUE_OPTIONAL,
					'Generate tree view of classes, interfaces, traits and exceptions.', TRUE)
			]);
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {
			$configFileOptions = $this->getConfigFileOptions($input);

			// cli has priority over config file
			$options = array_merge($input->getOptions(), $configFileOptions);
			$options = $this->unsetConsoleOptions($options);
			$options = $this->configuration->resolveOptions($options);

			$files = $this->scan($options, $output);
			$this->parse($options, $output, $files);
			$this->generate($options, $output);
			return 0;

		} catch (\Exception $e) {
			$output->writeln(PHP_EOL . '<error>' . $e->getMessage() . '</error>');
			return 1;
		}
	}


	/**
	 * @return SplFileInfo[]
	 */
	private function scan(array $options, OutputInterface $output)
	{
		foreach ($options[CO::SOURCE] as $source) {
			$output->writeln("<info>Scanning $source</info>");
		}

		foreach ($options[CO::EXCLUDE] as $exclude) {
			$output->writeln("<info>Excluding $exclude</info>");
		}

		return $this->scanner->scan($options[CO::SOURCE], $options[CO::EXCLUDE], $options[CO::EXTENSIONS]);
	}


	private function parse(array $options, OutputInterface $output, array $files)
	{
		$output->writeln("<info>Parsing...</info>");

		$this->parser->parse($files);

		if (count($this->parser->getErrors())) {
			if ($options[CO::DEBUG]) {
				if ( ! is_dir(LOG_DIRECTORY)) {
					mkdir(LOG_DIRECTORY);
				}
				Debugger::$logDirectory = LOG_DIRECTORY;
				foreach ($this->parser->getErrors() as $e) {
					$logName = Debugger::log($e);
					$output->writeln("<error>Parse error occurred, exception was stored info $logName</error>");
				}

			} else {
				$output->writeln(PHP_EOL . '<error>Found ' . count($this->parser->getErrors()) . ' errors.'
					. ' For more details add --debug option</error>');
			}
		}

		$stats = $this->parser->getDocumentedStats();
		$output->writeln(PHP_EOL . sprintf(
			'Generating documentation for %d classes, %d constants, %d functions and %d PHP internal classes.',
			$stats['classes'], $stats['constants'], $stats['functions'], $stats['internalClasses']
		));
	}


	private function generate(array $options, OutputInterface $output)
	{
		$output->writeln('<info>Wiping out destination directory</info>');
		$this->wiper->wipOutDestination();

		$output->writeln('<info>Generating to directory ' . $options[CO::DESTINATION] . '</info>');
		$skipping = array_merge($options[CO::SKIP_DOC_PATH], $options[CO::SKIP_DOC_PREFIX]); // @todo better merge
		foreach ($skipping as $skip) {
			$output->writeln("<info>Will not generate documentation for  $skip</info>");
		}

		$this->generator->generate();
		$this->generatorQueue->run();

		$output->writeln(PHP_EOL . '<info>Api was successfully generated!</info>');
	}


	/**
	 * @return array
	 */
	private function getConfigFileOptions(InputInterface $input)
	{
		$file = $input->getOption(CO::CONFIG);
		$neonFile = new NeonFile($file);
		$neonFile->validate();
		return $neonFile->read();
	}


	/**
	 * @return array
	 */
	private function unsetConsoleOptions(array $options)
	{
		unset($options[CO::CONFIG], $options['help'], $options['version'], $options['quiet'], $options['working-dir']);
		return $options;
	}

}

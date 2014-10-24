<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Command;

use ApiGen\Configuration\Configuration;
use ApiGen\Factory;
use ApiGen\FileSystem\Wiper;
use ApiGen\Generator\Generator;
use ApiGen\Git\VersionSwitcher;
use ApiGen\Neon\NeonFile;
use ApiGen\Parser\Parser;
use ApiGen\Scanner\Scanner;
use Nette\Utils\Strings;
use SplFileInfo;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * Generates the API documentation.
 */
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
	 * @var OutputInterface
	 */
	private $output;

	/**
	 * @var VersionSwitcher
	 */
	private $gitVersionSwitcher;


	public function __construct(Generator $generator, Wiper $wiper, Configuration $configuration, Scanner $scanner,
	                            Parser $parser, VersionSwitcher $gitVersionSwitcher)
	{
		parent::__construct();
		$this->generator = $generator;
		$this->wiper = $wiper;
		$this->configuration = $configuration;
		$this->scanner = $scanner;
		$this->parser = $parser;
		$this->gitVersionSwitcher = $gitVersionSwitcher;
	}


	protected function configure()
	{
		$this->setName('generate')
			->setDescription('Generate API documentation')
			->setDefinition(array(
				new InputArgument('destination', InputArgument::OPTIONAL, 'Target dir for documentation.', NULL),
				new InputArgument('source', InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
					'Dir(s) or file(s) documentation is generated for (separate multiple items with a space).', NULL),
				new InputOption('debug', 'd', InputOption::VALUE_NONE, 'Turn on debug mode.')
			));
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {
			$this->output = $output;
			$file = Factory::getApiGenFile();

			$neonFile = new NeonFile($file);
			$neonFile->validate();
			$apigen = $neonFile->read();

			$apigen['destination'] = $this->getValueFromArgumentOrConfig($input, $apigen, 'destination');
			$apigen['source'] = $this->getValueFromArgumentOrConfig($input, $apigen, 'source');

			$neonFile->write($apigen);

			$apigen['debug'] = $this->getValueFromOptionOrConfig($input, $apigen, 'debug');

			if (count($apigen['git']['versions'])) {
				$this->gitVersionSwitcher->setSource($apigen['source'][0]);

				$destination = $apigen['destination'];
				foreach ($apigen['git']['versions'] as $version) {
					$apigen['destination'] = $destination . DS . Strings::webalize($version) . DS;
					$apigen = $this->configuration->setDefaults($apigen);
					$this->gitVersionSwitcher->switchToVersion($version);
					$output->writeln('<comment>Generating for git version ' . $version . '</comment>');
					$this->runGeneration($apigen);
				}

				$this->gitVersionSwitcher->restoreInitBranch();

			} else {
				$apigen = $this->configuration->setDefaults($apigen);
				$this->runGeneration($apigen);
			}

			return 0;

		} catch (\Exception $e) {
			$output->writeln(PHP_EOL . '<error>' . $e->getMessage() . '</error>');
			return 1;
		}
	}


	/**
	 * @return SplFileInfo[]
	 */
	private function scan(array $apigen)
	{
		foreach ($apigen['source'] as $source) {
			$this->output->writeln("<info>Scanning $source</info>");
		}

		foreach ($apigen['exclude'] as $exclude) {
			$this->output->writeln("<info>Excluding $exclude</info>");
		}

		return $this->scanner->scan($apigen['source'], $apigen['exclude'], $apigen['extensions']);
	}


	private function parse(array $apigen, array $files)
	{
		$this->output->writeln("<info>Parsing...</info>");

		$this->parser->parse($files);

		if (count($this->parser->getErrors())) {
			if ($apigen['debug']) {
				if ( ! is_dir(LOG_DIRECTORY)) {
					mkdir(LOG_DIRECTORY);
				}
				Debugger::$logDirectory = LOG_DIRECTORY;
				foreach ($this->parser->getErrors() as $e) {
					$logName = Debugger::log($e);
					$this->output->writeln("<error>Parse error occurred, exception was stored info $logName</error>");
				}

			} else {
				$this->output->writeln(PHP_EOL . '<error>Found ' . count($this->parser->getErrors()) . ' errors.'
					. ' For more details add --debug option</error>');
			}
		}

		$stats = $this->parser->getDocumentedStats();
		$this->output->writeln(PHP_EOL . sprintf(
			'Generating documentation for %d classes, %d constants, %d functions and %d PHP internal classes.',
			$stats['classes'], $stats['constants'], $stats['functions'], $stats['internalClasses']
		));
	}


	private function generate(array $apigen)
	{
		$this->output->writeln('<info>Wiping out destination directory</info>');
		$this->wiper->wipOutDestination();

		$this->output->writeln('<info>Generating to directory \'' . $apigen['destination'] . '\'</info>');
		$skipping = array_merge($apigen['skipDocPath'], $apigen['skipDocPrefix']); // @todo better merge
		foreach ($skipping as $skip) {
			$this->output->writeln("<info>Will not generate documentation for  $skip</info>");
		}

		$this->generator->generate();

		$this->output->writeln(PHP_EOL . '<info>Api was successfully generated!</info>');
	}


	private function runGeneration(array $apigen)
	{
		$files = $this->scan($apigen);
		$this->parse($apigen, $files);
		$this->generate($apigen);
	}

}

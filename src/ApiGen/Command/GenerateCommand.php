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


	public function __construct(Generator $generator, Wiper $wiper, Configuration $configuration, Scanner $scanner,
	                            Parser $parser)
	{
		parent::__construct();
		$this->generator = $generator;
		$this->wiper = $wiper;
		$this->configuration = $configuration;
		$this->scanner = $scanner;
		$this->parser = $parser;
	}


	protected function configure()
	{
		$this->setName('generate')
			->setDescription('Generate API documentation')
			->setDefinition(array(
				new InputArgument('destination', InputArgument::OPTIONAL, 'Target dir for documentation.', NULL),
				new InputArgument('source', InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
					'Dir(s) or file(s) documentation is generated for (separate multiple items with a space).', NULL),
				new InputOption('debug', 'd', InputArgument::OPTIONAL, 'Turn on debug mode.', FALSE)
			));
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {
			$file = Factory::getApiGenFile();

			$neonFile = new NeonFile($file);
			$neonFile->validate();
			$apigen = $neonFile->read();

			$apigen['destination'] = $this->getArgumentValue($input, $apigen, 'destination');
			$apigen['source'] = $this->getArgumentValue($input, $apigen, 'source');

			$neonFile->write($apigen);

			$apigen['debug'] = $this->getOptionValue($input, $apigen, 'debug');

			$apigen = $this->configuration->setDefaults($apigen);

			$files = $this->scan($apigen, $output);
			$this->parse($apigen, $output, $files);
			$this->generate($apigen, $output);
			return 0;

		} catch (\Exception $e) {
			$output->writeln(PHP_EOL . '<error>' . $e->getMessage() . '</error>');
			return 1;
		}
	}


	/**
	 * @param array $apigen
	 * @param OutputInterface $output
	 * @return SplFileInfo[]
	 */
	private function scan(array $apigen, OutputInterface $output)
	{
		foreach ($apigen['source'] as $source) {
			$output->writeln("<info>Scanning $source</info>");
		}

		foreach ($apigen['exclude'] as $exclude) {
			$output->writeln("<info>Excluding $exclude</info>");
		}

		return $this->scanner->scan($apigen['source'], $apigen['exclude'], $apigen['extensions']);
	}


	private function parse(array $apigen, OutputInterface $output, array $files)
	{
		$output->writeln("<info>Parsing...</info>");

		$this->parser->parse($files);

		if (count($this->parser->getErrors())) {
			$output->writeln(PHP_EOL . '<error>Found ' . count($this->parser->getErrors()) . ' errors</error>');


			foreach ($this->parser->getErrors() as $e) {
				if ($apigen['debug']) {
					Debugger::$logDirectory = LOG_DIRECTORY;
					$logName = Debugger::log($e);
					$output->writeln("<error>Parse error occurred, exception was stored info $logName</error>");
				}
			}
		}

		$stats = $this->parser->getDocumentedStats();
		$output->writeln(PHP_EOL . sprintf(
			'Generating documentation for %d classes, %d constants, %d functions and %d PHP internal classes.',
			$stats['classes'], $stats['constants'], $stats['functions'], $stats['internalClasses']
		));
	}


	private function generate(array $apigen, OutputInterface $output)
	{
		// wipeout first
		$output->writeln('<info>Wiping out destination directory</info>');
		$this->wiper->wipOutDestination();

		$output->writeln('<info>Generating to directory \'' . $apigen['destination'] . '\'</info>');
		$skipping = array_merge($apigen['skipDocPath'], $apigen['skipDocPrefix']); // @todo better merge
		foreach ($skipping as $skip) {
			$output->writeln("<info>Will not generate documentation for  $skip</info>");
		}

		$this->generator->generate();

		$output->writeln(PHP_EOL . '<info>Api was successfully generated!</info>');
	}


	/**
	 * Gets value primary from input, secondary from config file.
	 *
	 * @throws InvalidArgumentException
	 * @param InputInterface $input
	 * @param array $apigen
	 * @param string $key
	 */
	private function getArgumentValue(InputInterface $input, array $apigen, $key)
	{
		if ($input->getArgument($key) === NULL && ! isset($apigen[$key])) {
			throw new InvalidArgumentException(ucfirst($key) . " is missing. Add it via apigen.neon or '$key' argument.");
		}

		return $input->getArgument($key) ?: $apigen[$key];
	}


	/**
	 * Gets value primary from input, secondary from config file.
	 *
	 * @throws InvalidArgumentException
	 * @param InputInterface $input
	 * @param array $apigen
	 * @param string $key
	 * @return mixed
	 */
	private function getOptionValue(InputInterface $input, array $apigen, $key)
	{
		if ($input->getOption($key) === NULL && ! isset($apigen[$key])) {
			throw new InvalidArgumentException(ucfirst($key) . " is missing. Add it via apigen.neon or '--$key' option.");
		}

		return ($input->getOption($key) !== NULL) ? $input->getOption($key) : $apigen[$key];
	}

}

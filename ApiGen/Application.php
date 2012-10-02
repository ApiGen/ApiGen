<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

use ApiGen;
use ApiGen\Config\Configuration;
use DateTime;
use Exception;
use TokenReflection;

/**
 * ApiGen application.
 *
 * Resposible for the documentation generator run.
 */
class Application extends Object
{
	/**
	 * Logger service.
	 *
	 * @var \ApiGen\ILogger
	 */
	private $logger;

	/**
	 * Application configuration.
	 *
	 * @var \ApiGen\Config\Configuration
	 */
	private $config;

	/**
	 * Generator service.
	 *
	 * @var \ApiGen\IGenerator
	 */
	private $generator;

	/**
	 * Creates an instance.
	 *
	 * @param \ApiGen\Config\Configuration $config Application configuration
	 * @param \ApiGen\ILogger $logger Logger service
	 * @param \ApiGen\IGenerator $generator Generator service
	 * @param \ApiGen\IErrorHandler $errorHandler Error handler service
	 */
	public function __construct(Configuration $config, ILogger $logger, IGenerator $generator)
	{
		$this->config = $config;
		$this->logger = $logger;
		$this->generator = $generator;
	}

	/**
	 * Starts the application.
	 */
	public function run()
	{
		try {
			$start = new DateTime();

			$name = Environment::getApplicationName() . ' ' . Environment::getApplicationVersion();
			$this->logger->log("%header\n", $name, str_repeat('-', strlen($name)) . "\n");

			$this->fireEvent('startup');

			if ($this->config->help) {
				// Print help if requested
				$this->printHelp();
			} else {

				// Scan and parse sources
				$this->parse();

				$this->logger->log("Generating to directory %value\n", $this->config->destination);

				// Wipeout the destination directory
				$this->wipeout();

				// Generate the API documentation
				$this->generate();

				// Prints the elapsed time
				$this->printElapsed($start, new DateTime());
			}

			$this->fireEvent('shutdown');

		} catch (Exception $e) {
			$this->fireEvent('error', $e);
		}
	}

	/**
	 * Scans and parses the source codes.
	 *
	 * @return \ApiGen\Application
	 */
	protected function parse()
	{
		// Scan
		if (count($this->config->source) > 1) {
			$this->logger->log("Scanning\n %value", implode("\n ", $this->config->source) . "\n");
		} else {
			$this->logger->log("Scanning %value\n", $this->config->source[0]);
		}

		if (count($this->config->exclude) > 1) {
			$this->logger->log("Excluding\n %value", implode("\n ", $this->config->exclude) . "\n");
		} elseif (count($this->config->exclude) === 1) {
			$this->logger->log("Excluding %value\n", $this->config->exclude[0]);
		}

		$parseInfo = $this->generator->parse();

		if (count($parseInfo->errors) > 1) {
			$this->logger->log("%error\n\n", sprintf('Found %d errors', count($parseInfo->errors)));

			$no = 1;
			foreach ($parseInfo->errors as $e) {
				if ($e instanceof TokenReflection\Exception\ParseException) {
					$this->logger->log("%error. The TokenReflection library threw an exception while parsing the file %value.\n", $no, $e->getFileName());
					if ($this->config->debug) {
						$this->logger->log("\nThis can have two reasons: a) the source code in the file is not valid or b) you have just found a bug in the TokenReflection library.\n\n");
						$this->logger->log("If the license allows it please send the whole file or at least the following fragment describing where exacly is the problem along with the backtrace to apigen@apigen.org. Thank you!\n\n");

						$token = $e->getToken();
						$sender = $e->getSender();
						if (!empty($token)) {
							$this->logger->log(
								"The cause of the exception \"{$e->getMessage()}\" was the %value token (line %number) in the following part of ",
								$e->getTokenName(),
								$e->getExceptionLine(),
								($sender && $sender->getName() ? '%value' : ''),
								($sender && $sender->getName() ? $sender->getPrettyName() : ''),
								($sender && $sender->getName() ? '' : 'the'),
								" source code:\n\n"
							);
						} else {
							$this->logger->log(
								"The exception \"{$e->getMessage()}\" was thrown when processing ",
								($sender && $sender->getName() ? '%value' : ''),
								($sender && $sender->getName() ? $sender->getPrettyName() : ''),
								($sender && $sender->getName() ? '' : 'the'),
								" source code:\n\n"
							);
						}

						$this->logger->log($e->getSourcePart(true) . "\n\nThe exception backtrace is following:\n\n" . $e->getTraceAsString() . "\n\n");
					}
				} elseif ($e instanceof TokenReflection\Exception\FileProcessingException) {
					$this->logger->log("%error. {$e->getMessage()}\n", $no);
					if ($this->config->debug) {
						$this->logger->log("\n{$e->getDetail()}\n\n");
					}
				} else {
					$this->logger->log("%error. {$e->getMessage()}\n", $no);
					if ($this->config->debug) {
						$trace = $e->getTraceAsString();
						while ($e = $e->getPrevious()) {
							$this->logger->log("\n" . $e->getMessage());
							$trace = $e->getTraceAsString();
						}
						$this->logger->log("\n$trace\n\n");
					}
				}

				$no++;
			}

			if (!$this->config->debug) {
				$this->logger->log("\nEnable the debug mode (%option) to see more details.\n\n", '--debug');
			}
		}

		$this->logger->log("Found %number classes, %number constants, %number functions and other %number used PHP internal classes\n", (int) $parseInfo->classes, (int) $parseInfo->constants, (int) $parseInfo->functions, (int) $parseInfo->internalClasses);
		$this->logger->log("Documentation for %number classes, %number constants, %number functions and other %number used PHP internal classes will be generated\n", (int) $parseInfo->documentedClasses, (int) $parseInfo->documentedConstants, (int) $parseInfo->documentedFunctions, (int) $parseInfo->documentedInternalClasses);

		return $this;
	}

	/**
	 * Wipes out the destination directory.
	 *
	 * @return \ApiGen\Application
	 */
	protected function wipeout()
	{
		if ($this->config->wipeout && is_dir($this->config->destination)) {
			$this->logger->log("Wiping out destination directory\n");
			if (!$this->generator->wipeOutDestination()) {
				throw new Exception('Cannot wipe out destination directory');
			}
		}

		return $this;
	}

	/**
	 * Generates the API documentation.
	 *
	 * @return \ApiGen\Application
	 */
	protected function generate()
	{
		$skipping = array_merge($this->config->skipDocPath->toArray(), $this->config->skipDocPrefix->toArray()); // @todo better merge
		if (count($skipping) > 1) {
			$this->logger->log("Will not generate documentation for\n %value\n", implode("\n ", $skipping));
		} elseif (!empty($skipping)) {
			$this->logger->log("Will not generate documentation for %value\n", $skipping[0]);
		}

		$this->generator->generate();

		return $this;
	}


	/**
	 * Prints help.
	 *
	 * @return \ApiGen\Application
	 */
	protected function printHelp()
	{
		$defaultTemplateConfig = Config\Helper::getDefaultTemplateConfigPath();

		$help = <<<"HELP"
Usage:
	apigen --config <path> [options]
	apigen --source <dir|file> --destination <dir> [options]

Options:
	--config|-c        <file>      Config file
	--source|-s        <dir|file>  Source file or directory to parse (can be used multiple times)
	--destination|-d   <dir>       Directory where to save the generated documentation
	--extensions       <list>      List of allowed file extensions, default "php"
	--exclude          <mask>      Mask (case sensitive) to exclude file or directory from processing (can be used multiple times)
	--skip-doc-path    <mask>      Don't generate documentation for elements from file or directory with this (case sensitive) mask (can be used multiple times)
	--skip-doc-prefix  <value>     Don't generate documentation for elements with this (case sensitive) name prefix (can be used multiple times)
	--charset          <list>      Character set of source files, default "auto"
	--main             <value>     Main project name prefix
	--title            <value>     Title of generated documentation
	--base-url         <value>     Documentation base URL
	--google-cse-id    <value>     Google Custom Search ID
	--google-cse-label <value>     Google Custom Search label
	--google-analytics <value>     Google Analytics tracking code
	--template-config  <file>      Template config file, default "$defaultTemplateConfig"
	--allowed-html     <list>      List of allowed HTML tags in documentation, default "b,i,a,ul,ol,li,p,br,var,samp,kbd,tt"
	--groups           <value>     How should elements be grouped in the menu. Default value is "auto" (namespaces if available, packages otherwise)
	--autocomplete     <list>      Element types for search input autocomplete. Default value is "@valuelasses,constants,functions"
	--access-levels    <list>      Generate documentation for methods and properties with given access level, default "public,protected"
	--internal         <yes|no>    Generate documentation for elements marked as internal and display internal documentation parts, default "no"
	--php              <yes|no>    Generate documentation for PHP internal classes, default "yes"
	--tree             <yes|no>    Generate tree view of classes, interfaces, traits and exceptions, default "yes"
	--deprecated       <yes|no>    Generate documentation for deprecated elements, default "no"
	--todo             <yes|no>    Generate documentation of tasks, default "no"
	--source-code      <yes|no>    Generate highlighted source code files, default "yes"
	--download         <yes|no>    Add a link to download documentation as a ZIP archive, default "no"
	--report           <file>      Save a checkstyle report of poorly documented elements into a file
	--wipeout          <yes|no>    Wipe out the destination directory first, default "yes"
	--plugin-config    <file>      Plugin config file (can be used multiple times)
	--quiet            <yes|no>    Don't display scaning and generating messages, default "no"
	--progressbar      <yes|no>    Display progressbars, default "yes"
	--colors           <yes|no>    Use colors, default "yes" in terminals with colors support
	--update-check     <yes|no>    Check for update, default "yes"
	--debug            <yes|no>    Display additional information in case of an error, default "no"
	--help|-h                      Display this help

Only source and destination directories are required - either set explicitly or using a config file. Configuration parameters passed via command line have precedence over parameters from a config file.

Boolean options (those with possible values yes|no) do not have to have their values defined explicitly. Using --debug and --debug=yes is exactly the same.

Some options can have multiple values. You can do so either by using them multiple times or by separating values by a comma. That means that writing --source=file1.php --source=file2.php or --source=file1.php,file2.php is exactly the same.

Files or directories specified by --exclude will not be processed at all.
Elements from files within --skip-doc-path or with --skip-doc-prefix will be parsed but will not have their documentation generated. However if classes have any child classes, the full class tree will be generated and their inherited methods, properties and constants will be displayed (but will not be clickable).

HELP;

		call_user_func(array($this->logger, 'log'), $help);

		return $this;
	}

	/**
	 * Prints the elapsed time.
	 *
	 * @param \DateTime $start Start time
	 * @param \DateTime $end End time
	 * @return \ApiGen\Application
	 */
	protected function printElapsed(DateTime $start, DateTime $end)
	{
		$interval = $end->diff($start);

		$parts = array('Done. Total time:');

		if ($interval->h > 0) {
			array_push($parts, ' %number hours', $interval->h);
		}
		if ($interval->i > 0) {
			array_push($parts, ' %number min', $interval->i);
		}
		if ($interval->s > 0) {
			array_push($parts, ' %number sec', $interval->s);
		}

		if (empty($parts)) {
			array_push($parts, ' %number sec', 1);
		}

		array_push($parts, ", used: %number MB RAM\n", round(memory_get_peak_usage(true) / 1024 / 1024));

		call_user_func_array(array($this->logger, 'log'), $parts);

		return $this;
	}
}

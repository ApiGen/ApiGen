<?php

namespace ApiGen;

use ApiGen;
use ApiGen\Config\Configuration;
use DateTime;
use Exception;
use Nette\Object;
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
	 * Error handling service.
	 *
	 * @var \ApiGen\IErrorHandler
	 */
	private $errorHandler;

	/**
	 * Latest version checker.
	 *
	 * @var \ApiGen\UpdateChecker
	 */
	private $updateChecker;

	/**
	 * Callbacks performed on application startup.
	 *
	 * @var array
	 */
	public $onStartup = array();

	/**
	 * Callbacks performed on application shutdown.
	 *
	 * @var array
	 */
	public $onShutdown = array();

	/**
	 * Creates an instance.
	 *
	 * @param \ApiGen\Config\Configuration $config Application configuration
	 * @param \ApiGen\ILogger $logger Logger service
	 * @param \ApiGen\IGenerator $generator Generator service
	 * @param \ApiGen\IErrorHandler $errorHandler Error handler service
	 */
	public function __construct(Configuration $config, ILogger $logger, IGenerator $generator, IErrorHandler $errorHandler)
	{
		$this->config = $config;
		$this->logger = $logger;
		$this->generator = $generator;
		$this->errorHandler = $errorHandler;
	}

	/**
	 * Sets the update checker service.
	 *
	 * @param \ApiGen\UpdateChecker $updateChecker Update checker
	 */
	public function setUpdateChecker(UpdateChecker $updateChecker)
	{
		$this->updateChecker = $updateChecker;
	}

	/**
	 * Starts the application.
	 */
	public function run()
	{
		$start = new DateTime();

		try {
			$name = Environment::getApplicationName();
			$this->logger->log("%h1\n", $name, str_repeat('-', strlen($name)) . "\n");

			$this->onStartup($this);

			// Update check
			$this->checkUpdates();

			// Scan and parse sources
			$this->parse();

			$this->logger->log("Generating to directory %value\n", $this->config->destination);

			// Wipeout the destination directory
			$this->wipeout();

			// Generate the API documentation
			$this->generate();

		} catch (Exception $e) {
			$this->errorHandler->handleException($e);
		}

		$this->onShutdown($this);

		// Prints the elapsed time
		$this->printElapsed($start, new DateTime());
	}

	/**
	 * Performs an update check.
	 *
	 * @return \ApiGen\Application
	 */
	protected function checkUpdates()
	{
		if (null !== $this->updateChecker) {
			$latestVersion = $this->updateChecker->getNewestVersion();
			if (!empty($latestVersion) && version_compare(VERSION, $latestVersion, '<')) {
				$this->logger->log("New version %h1 available\n\n", $latestVersion);
			}
		}

		return $this;
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
		}

		if (!$this->config->debug) {
			$this->logger->log("\nEnable the debug mode (%h2) to see more details.\n\n", '--debug');
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

		array_push($parts, ", used: %number MB RAM\n", round(memory_get_peak_usage(true) / 1024 / 1024));

		call_user_func_array(array($this->logger, 'log'), $parts);

		return $this;
	}
}

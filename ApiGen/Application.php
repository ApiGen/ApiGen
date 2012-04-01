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
			$this->onStartup($this);

			// Update check
			$this->checkUpdates();

			// Scan and parse sources
			$this->parse();

			$this->logger->log(sprintf("Generating to directory @value@%s@c\n", $this->config->destination));

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
			if (!empty($latestVersion) && version_compare(ApiGen\VERSION, $latestVersion, '<')) {
				$this->logger->log(sprintf("New version @header@%s@c available\n\n", $latestVersion));
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
			$this->logger->log(sprintf("Scanning\n @value@%s@c\n", implode("\n ", $this->config->source)));
		} else {
			$this->logger->log(sprintf("Scanning @value@%s@c\n", $this->config->source[0]));
		}

		if (count($this->config->exclude) > 1) {
			$this->logger->log(sprintf("Excluding\n @value@%s@c\n", implode("\n ", $this->config->exclude)));
		} elseif (count($this->config->exclude) === 1) {
			$this->logger->log(sprintf("Excluding @value@%s@c\n", $this->config->exclude[0]));
		}

		$parseInfo = $this->generator->parse();

		if (count($parseInfo->errors) > 1) {
			$this->logger->log(sprintf("@error@Found %d errors@c\n\n", count($parseInfo->errors)));

			$no = 1;
			foreach ($parseInfo->errors as $e) {
				if ($e instanceof TokenReflection\Exception\ParseException) {
					$this->logger->log(sprintf("@error@%d.@c The TokenReflection library threw an exception while parsing the file @value@%s@c.\n", $no, $e->getFileName()));
					if ($this->config->debug) {
						$this->logger->log("\nThis can have two reasons: a) the source code in the file is not valid or b) you have just found a bug in the TokenReflection library.\n\n");
						$this->logger->log("If the license allows it please send the whole file or at least the following fragment describing where exacly is the problem along with the backtrace to apigen@apigen.org. Thank you!\n\n");

						$token = $e->getToken();
						$sender = $e->getSender();
						if (!empty($token)) {
							$this->logger->log(
								sprintf(
									"The cause of the exception \"%s\" was the @value@%s@c token (line @count@%d@c) in following part of %s source code:\n\n",
									$e->getMessage(),
									$e->getTokenName(),
									$e->getExceptionLine(),
									$sender && $sender->getName() ? '@value@' . $sender->getPrettyName() . '@c' : 'the'
								)
							);
						} else {
							$this->logger->log(
								sprintf(
									"The exception \"%s\" was thrown when processing %s source code:\n\n",
									$e->getMessage(),
									$sender && $sender->getName() ? '@value@' . $sender->getPrettyName() . '@c' : 'the'
								)
							);
						}

						$this->logger->log($e->getSourcePart(true) . "\n\nThe exception backtrace is following:\n\n" . $e->getTraceAsString() . "\n\n");
					}
				} elseif ($e instanceof TokenReflection\Exception\FileProcessingException) {
					$this->logger->log(sprintf("@error@%d.@c %s\n", $no, $e->getMessage()));
					if ($this->config->debug) {
						$this->logger->log("\n" . $e->getDetail() . "\n\n");
					}
				} else {
					$this->logger->log(sprintf("@error@%d.@c %s\n", $no, $e->getMessage()));
					if ($this->config->debug) {
						$trace = $e->getTraceAsString();
						while ($e = $e->getPrevious()) {
							$this->logger->log(sprintf("\n%s", $e->getMessage()));
							$trace = $e->getTraceAsString();
						}
						$this->logger->log(sprintf("\n%s\n\n", $trace));
					}
				}

				$no++;
			}
		}

		if (!$this->config->debug) {
			$this->logger->log("\nEnable the debug mode (@option@--debug@c) to see more details.\n\n");
		}

		$this->logger->log(sprintf("Found @count@%d@c classes, @count@%d@c constants, @count@%d@c functions and other @count@%d@c used PHP internal classes\n", $parseInfo->classes, $parseInfo->constants, $parseInfo->functions, $parseInfo->internalClasses));
		$this->logger->log(sprintf("Documentation for @count@%d@c classes, @count@%d@c constants, @count@%d@c functions and other @count@%d@c used PHP internal classes will be generated\n", $parseInfo->documentedClasses, $parseInfo->documentedConstants, $parseInfo->documentedFunctions, $parseInfo->documentedInternalClasses));

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
			$this->logger->log(sprintf("Will not generate documentation for\n @value@%s@c\n", implode("\n ", $skipping)));
		} elseif (!empty($skipping)) {
			$this->logger->log(sprintf("Will not generate documentation for @value@%s@c\n", $skipping[0]));
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

		$parts = array();
		if ($interval->h > 0) {
			$parts[] = sprintf('@count@%d@c hours', $interval->h);
		}
		if ($interval->i > 0) {
			$parts[] = sprintf('@count@%d@c min', $interval->i);
		}
		if ($interval->s > 0) {
			$parts[] = sprintf('@count@%d@c sec', $interval->s);
		}
		$duration = implode(' ', $parts);

		$this->logger->log(sprintf("Done. Total time: %s, used: @count@%d@c MB RAM\n", $duration, round(memory_get_peak_usage(true) / 1024 / 1024)));

		return $this;
	}
}

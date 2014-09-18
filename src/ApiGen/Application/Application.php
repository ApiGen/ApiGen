<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Application;

use ApiGen;
use ApiGen\Configuration\Configuration;
use ApiGen\Generator\Generator;
use ApiGen\Logger;
use DateTime;
use Exception;
use Nette\Object;
use TokenReflection;


/**
 * @method  Application   onStartup(Application $app)
 * @method  Application   onShutdown(Application $app)
 * @method  Application   onError(\Exception $e)
 */
class Application extends Object
{

	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @var Configuration
	 */
	private $config;

	/**
	 * @var Generator
	 */
	private $generator;

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
	 * Callbacks performed on application error.
	 *
	 * @var array
	 */
	public $onError = array();


	public function __construct(Configuration $config, Logger $logger, Generator $generator)
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
			$start = new DateTime;

			$headline = ApiGen\ApiGen::NAME . ' ' . ApiGen\ApiGen::VERSION;
			$this->logger->log($headline . "\n");
			$this->logger->log(str_repeat('-', strlen($headline)) . "\n");

			$this->onStartup($this);

			$this->scan();

			$this->parse();

			$this->logger->log(sprintf("Generating to directory %s\n", $this->config->destination));

			$this->wipeout();

			$this->generate();

			$this->onShutdown($this);

			$this->printElapsed($start, new DateTime());

		} catch (Exception $e) {
			$this->onError($e);
		}
	}


	protected function scan()
	{
		$this->generator->scan((array) $this->config->source, (array) $this->config->exclude, (array) $this->config->extensions);

		foreach ($this->config->source as $source) {
			$this->logger->log(sprintf("Scanning\n %s\n", $source));
		}

		foreach ($this->config->exclude as $exclude) {
			$this->logger->log(sprintf("Excluding\n %s\n", $exclude));
		}
	}


	protected function parse()
	{
		if (count($this->config->source) > 1) {
			$this->logger->log(sprintf("Scanning\n %s\n", implode("\n ", $this->config->source)));

		} else {
			$this->logger->log(sprintf("Scanning %s\n", $this->config->source[0]));
		}

		if (count($this->config->exclude) > 1) {
			$this->logger->log(sprintf("Excluding\n %s\n", implode("\n ", $this->config->exclude)));

		} elseif (count($this->config->exclude) === 1) {
			$this->logger->log(sprintf("Excluding %s\n", $this->config->exclude[0]));
		}

		$parseInfo = $this->generator->parse();

		if (count($parseInfo->errors) > 1) {
			$this->logger->log(sprintf("@error@Found %d errors\n\n", count($parseInfo->errors)));

			$no = 1;
			foreach ($parseInfo->errors as $e) {
				if ($e instanceof TokenReflection\Exception\ParseException) {
					$this->logger->log(sprintf("%d. The TokenReflection library threw an exception while parsing the file %s.\n", $no, $e->getFileName()));

					if ($this->config->debug) {
						$this->logger->log("\nThe source code in the file is not valid.\n\n");

						$token = $e->getToken();
						$sender = $e->getSender();
						if ( ! empty($token)) {
							$this->logger->log(
								sprintf(
									"The cause of the exception \"%s\" was the %s token (line %d) in following part of %s source code:\n\n",
									$e->getMessage(),
									$e->getTokenName(),
									$e->getExceptionLine(),
									$sender && $sender->getName() ? '' . $sender->getPrettyName() . '' : 'the'
								)
							);

						} else {
							$this->logger->log(
								sprintf(
									"The exception \"%s\" was thrown when processing %s source code:\n\n",
									$e->getMessage(),
									$sender && $sender->getName() ? '' . $sender->getPrettyName() . '' : 'the'
								)
							);
						}

						$this->logger->log($e->getSourcePart(TRUE) . "\n\nThe exception backtrace is following:\n\n" . $e->getTraceAsString() . "\n\n");
					}

				} elseif ($e instanceof TokenReflection\Exception\FileProcessingException) {
					$this->logger->log(sprintf("@error@%d. %s\n", $no, $e->getMessage()));
					if ($this->config->debug) {
						$this->logger->log("\n" . $e->getDetail() . "\n\n");
					}

				} else {
					/** @var Exception $e */
					$this->logger->log(sprintf("@error@%d. %s\n", $no, $e->getMessage()));
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

			if ( ! $this->config->debug) {
				$this->logger->log("\nEnable the debug mode (--debug) to see more details.\n\n");
			}
		}

		$this->logger->log(sprintf("Found %d classes, %d constants, %d functions and other %d used PHP internal classes\n", $parseInfo->classes, $parseInfo->constants, $parseInfo->functions, $parseInfo->internalClasses));
		$this->logger->log(sprintf("Documentation for %d classes, %d constants, %d functions and other %d used PHP internal classes will be generated\n", $parseInfo->documentedClasses, $parseInfo->documentedConstants, $parseInfo->documentedFunctions, $parseInfo->documentedInternalClasses));
	}


	/**
	 * Wipes out the destination directory.
	 *
	 * @throws Exception
	 */
	protected function wipeout()
	{
		if ($this->config->wipeout && is_dir($this->config->destination)) {
			$this->logger->log("Wiping out destination directory\n");

			if ( ! $this->generator->wipeOutDestination()) {
				throw new Exception('Cannot wipe out destination directory');
			}
		}
	}


	/**
	 * Generates the API documentation.
	 */
	protected function generate()
	{
		$skipping = array_merge((array) $this->config->skipDocPath, (array) $this->config->skipDocPrefix); // @todo better merge
		if (count($skipping) > 1) {
			$this->logger->log(sprintf("Will not generate documentation for\n %s\n", implode("\n ", $skipping)));

		} elseif ( ! empty($skipping)) {
			$this->logger->log(sprintf("Will not generate documentation for %s\n", $skipping[0]));
		}

		$this->generator->generate();
	}


	/**
	 * Prints the elapsed time.
	 */
	protected function printElapsed(DateTime $start, DateTime $end)
	{
		$interval = $end->diff($start);

		$parts = array();
		if ($interval->h > 0) {
			$parts[] = sprintf('%d hours', $interval->h);
		}

		if ($interval->i > 0) {
			$parts[] = sprintf('%d min', $interval->i);
		}

		if ($interval->s > 0) {
			$parts[] = sprintf('%d sec', $interval->s);
		}

		if (empty($parts)) {
			array_push($parts, ' %d sec', 1);
		}

		$duration = implode(' ', $parts);

		$this->logger->log(sprintf("Done. Total time: %s, used: %d MB RAM\n", $duration, round(memory_get_peak_usage(TRUE) / 1024 / 1024)));
	}

}

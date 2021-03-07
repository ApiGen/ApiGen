<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Info\ClassInfo;
use ApiGenX\TaskExecutor\TaskExecutor;
use ApiGenX\Tasks\AnalyzeTask;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Symfony\Component\Console\Helper\ProgressBar;


final class Analyzer
{
	public function __construct(
		private Locator $locator,
		private LoopInterface $loop,
		private TaskExecutor $taskExecutor,
	) {
	}


	/**
	 * @param  string[] $files indexed by []
	 */
	public function analyze(ProgressBar $progressBar, array $files): PromiseInterface
	{
		$deferred = new Deferred();

		$analyzed = [];
		$found = [];
		$missing = [];
		$waiting = 0;
		$scheduled = false;

		$processResult = function (array $result) use (&$schedule, &$found, &$missing) {
			foreach ($result as $info) {
				foreach ($info->dependencies as $dependency) {
					if (!isset($found[$dependency->fullLower]) && !isset($missing[$dependency->fullLower])) {
						$missing[$dependency->fullLower] = $info;
						$file = $this->locator->locate($dependency);

						if ($file !== null) {
							$schedule($file, false);
						}
					}
				}

				unset($missing[$info->name->fullLower]);
				$found[$info->name->fullLower] = $info;
			}

			return $result;
		};

		$processWaiting = function (array $result) use ($deferred, &$found, &$missing, &$waiting, &$scheduled, $progressBar) {
			$progressBar->advance();

			if (--$waiting === 0  && $scheduled) {
				dump(sprintf('Found: %d', count($found)));
				dump(sprintf('Missing: %d', count($missing)));

				foreach ($missing as $fullLower => $dependencyOf) {
					$dependency = $dependencyOf->dependencies[$fullLower];

					dump("Missing {$dependency->full} required by {$dependencyOf->name->full}");

					$info = new ClassInfo($dependency); // TODO: mark as missing
					$info->primary = false;
					$found[$info->name->fullLower] = $info;
				}

				$deferred->resolve($found);
			}

			return $result;
		};

		$schedule = function (string $file, bool $isPrimary) use (&$analyzed, &$waiting, $processResult, $processWaiting, $progressBar) {
			$file = realpath($file);

			if (!isset($analyzed[$file])) {
				$analyzed[$file] = true;
				$progressBar->setMaxSteps($progressBar->getMaxSteps() + 1);
				$waiting++;
				$this->taskExecutor->process(new AnalyzeTask($file, $isPrimary))->then($processResult)->then($processWaiting);
			}
		};

		foreach ($files as $i => $file) {
			$scheduled = $i === count($files) - 1;
			$schedule($file, true);
		}

		return $deferred->promise();
	}
}

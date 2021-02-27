<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Info\ClassInfo;
use ApiGenX\TaskExecutor\TaskExecutor;
use ApiGenX\Tasks\AnalyzeTask;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;


final class Analyzer
{
	public function __construct(private LoopInterface $loop, private TaskExecutor $taskExecutor)
	{
	}


	/**
	 * @param  string[] $files indexed by []
	 * @param  callable(string $classLikeName): ?string $autoloader
	 */
	public function analyze(array $files, callable $autoloader): PromiseInterface
	{
		$deferred = new Deferred();

		$analyzed = [];
		$found = [];
		$missing = [];
		$waiting = 0;

		$processResult = function (array $result) use ($autoloader, &$schedule, &$found, &$missing) {
			foreach ($result as $info) {
				foreach ($info->dependencies as $dependency) {
					if (!isset($found[$dependency->fullLower]) && !isset($missing[$dependency->fullLower])) {
						$missing[$dependency->fullLower] = $dependency;
						$file = $autoloader($dependency->full);

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

		$processWaiting = function (array $result) use ($deferred, &$found, &$missing, &$waiting) {
			if (--$waiting === 0) {
				dump(sprintf('Found: %d', count($found)));
				dump(sprintf('Missing: %d', count($missing)));

				foreach ($missing as $dependency) {
					$info = new ClassInfo($dependency); // TODO: mark as missing
					$info->primary = false;
					$found[$info->name->fullLower] = $info;
				}

				$deferred->resolve($found);
			}

			return $result;
		};

		$schedule = function (string $file, bool $isPrimary) use (&$analyzed, &$waiting, $processResult, $processWaiting) {
			$file = realpath($file);

			if (!isset($analyzed[$file])) {
				$analyzed[$file] = true;
				$waiting++;
				$this->taskExecutor->process(new AnalyzeTask($file, $isPrimary))->then($processResult)->then($processWaiting);
			}
		};

		foreach ($files as $file) {
			$schedule($file, true);
		}

		return $deferred->promise();
	}
}

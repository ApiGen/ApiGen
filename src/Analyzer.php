<?php declare(strict_types = 1);

namespace ApiGenX;

use Amp;
use Amp\Parallel\Worker\BasicEnvironment;
use Amp\Parallel\Worker\DefaultPool;
use ApiGenX\Info\ClassInfo;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Tasks\AnalyzeTask;


final class Analyzer
{
	/**
	 * @param  string[]                                 $files      indexed by []
	 * @param  callable(string $classLikeName): ?string $autoloader
	 * @return ClassLikeInfo[]|iterable
	 */
	public function analyze(array $files, callable $autoloader): iterable
	{
		$async = true; // TODO

		$env = new BasicEnvironment();
		$pool = new DefaultPool(8); // TODO: worker count

		$promises = [];
		$found = [];
		$missing = [];

		foreach ($files as $file) {
			$file = realpath($file);
			$task = new AnalyzeTask($file, true);
			$promises[$file] ??= $async ? $pool->enqueue($task) : new Amp\Success($task->run($env));
		}

		while (current($promises) !== false) {
			$dependencies = [];

			while (($promise = current($promises)) !== false) {
				next($promises);

				foreach (Amp\Promise\wait($promise) as $info) { // TODO: try order-independent result processing
					foreach ($info->dependencies as $dependency) {
						if (!isset($found[$dependency->fullLower])) {
							$missing[$dependency->fullLower] = $dependency;
							$dependencies[$dependency->fullLower] = $dependency;
						}
					}

					if ($info instanceof ClassLikeInfo) {
						unset($dependencies[$info->name->fullLower], $missing[$info->name->fullLower]);
						$found[$info->name->fullLower] = $info;
						yield $info;

					} else {
						throw new \LogicException(); // TODO: functions and constants
					}
				}
			}

			foreach ($dependencies as $dependency) {
				$file = $autoloader($dependency->full);

				if ($file !== null) {
					$file = realpath($file);
					$task = new AnalyzeTask($file, false);
					$promises[$file] ??= $async ? $pool->enqueue($task) : new Amp\Success($task->run($env));
				}
			}
		}

		foreach ($missing as $dependency) {
			dump("MISSING: {$dependency->full}");
			$info = new ClassInfo($dependency); // TODO: mark as missing
			$info->primary = false;

			yield $info;
		}
	}


	/**
	 * @param  string[]                                 $files      indexed by []
	 * @param  callable(string $classLikeName): ?string $autoloader
	 * @return ClassLikeInfo[]|iterable
	 */
	public function analyzeX(array $files, callable $autoloader): iterable
	{
		$async = false; // TODO

		$env = new BasicEnvironment();
		$pool = new DefaultPool(16); // TODO: worker count

		$promises = [];
		$found = [];
		$missing = [];
		$waiting = 0;

		$onResolve = function ($error, array $result) use ($autoloader, $async, $env, $pool, &$promises, &$found, &$missing, &$waiting, &$onResolve) {
			foreach ($result as $info) {
				foreach ($info->dependencies as $dependency) {
					if (!isset($found[$dependency->fullLower])) {
						$missing[$dependency->fullLower] = $dependency;
						$file = $autoloader($dependency->full);

						if ($file !== null) {
							$file = realpath($file);

							if (!isset($promises[$file])) {
								$task = new AnalyzeTask($file, false);
								$promises[$file] = $async ? $pool->enqueue($task) : new Amp\Success($task->run($env));
								$promises[$file]->onResolve($onResolve);
								$waiting++;
							}
						}
					}
				}

				unset($missing[$info->name->fullLower]);
				$found[$info->name->fullLower] = $info;
			}

			$waiting--;

			if ($waiting === 0) {
				Amp\Loop::stop();
			}
		};

		foreach ($files as $file) {
			$file = realpath($file);

			if (!isset($promises[$file])) {
				$task = new AnalyzeTask($file, true);
				$promises[$file] = $async ? $pool->enqueue($task) : new Amp\Success($task->run($env));
				$promises[$file]->onResolve($onResolve);
				$waiting++;
			}
		}

		Amp\Loop::run();

		yield from $found;

		foreach ($missing as $dependency) {
			dump("MISSING: {$dependency->full}");
			$info = new ClassInfo($dependency); // TODO: mark as missing
			$info->primary = false;

			yield $info;
		}
	}
}

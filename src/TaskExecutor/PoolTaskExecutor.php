<?php declare(strict_types = 1);

namespace ApiGenX\TaskExecutor;

use React\Promise\PromiseInterface;
use SplPriorityQueue;


final class PoolTaskExecutor implements TaskExecutor
{
	private SplPriorityQueue $workers;

	private int $runningCount = 0;


	/**
	 * @param TaskExecutor[] $workers
	 */
	public function __construct(array $workers)
	{
		$this->workers = new SplPriorityQueue();

		foreach ($workers as $worker) {
			$this->workers->insert($worker, -$worker->runningCount());
		}
	}


	public static function create(int $count, callable $factory): self
	{
		$workers = [];

		while ($count--) {
			$workers[] = $factory();
		}

		return new self($workers);
	}


	public function runningCount(): int
	{
		return $this->runningCount;
	}


	public function process(Task $task): PromiseInterface
	{
		$this->runningCount++;

		/** @var TaskExecutor $worker */
		$worker = $this->workers->extract();
		$this->workers->insert($worker, -$worker->runningCount() - 1);

		return $worker->process($task)->then(
			function ($result) use ($worker) {
				$this->workers->insert($worker, -$worker->runningCount());
				$this->runningCount--;
				return $result;
			},
			function ($exception) use ($worker) {
				$this->workers->insert($worker, -$worker->runningCount());
				$this->runningCount--;
				throw $exception;
			}
		);
	}
}

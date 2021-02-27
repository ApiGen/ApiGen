<?php declare(strict_types = 1);

namespace ApiGenX\TaskExecutor;

use React\Promise\Deferred;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use SplQueue;


final class LimitTaskExecutor implements TaskExecutor
{
	private TaskExecutor $inner;

	private int $limit;

	private SplQueue $taskQueue;


	public function __construct(TaskExecutor $inner, int $limit)
	{
		$this->inner = $inner;
		$this->limit = $limit;
		$this->taskQueue = new SplQueue();
	}


	public function runningCount(): int
	{
		return $this->inner->runningCount() + $this->taskQueue->count();
	}


	public function process(Task $task): PromiseInterface
	{
		if ($this->inner->runningCount() >= $this->limit) {
			return new Promise(function (callable $resolve, callable $reject) use ($task): void {
				$this->taskQueue->enqueue([$task, $resolve, $reject]);
			});

		} else {
			return $this->inner->process($task)->then([$this, 'dequeue'], [$this, 'dequeue']);
		}
	}


	public function dequeue($res)
	{
		if (!$this->taskQueue->isEmpty()) {
			[$task, $resolve, $reject] = $this->taskQueue->dequeue();
			$this->inner->process($task)->then([$this, 'dequeue'], [$this, 'dequeue'])->then($resolve, $reject);
		}

		return $res; // TODO: res
	}
}

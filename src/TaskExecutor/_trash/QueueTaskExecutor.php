<?php declare(strict_types = 1);

namespace ApiGenX\TaskExecutor;

use React\Promise\Promise;
use React\Promise\PromiseInterface;
use SplQueue;


final class QueueTaskExecutor implements TaskExecutor
{
	private TaskExecutor $inner;

	private SplQueue $taskQueue;

	private ?Task $runningTask;

	private int $runningCount = 0;

	private int $limit;


	public function __construct(TaskExecutor $inner, int $limit)
	{
		$this->inner = $inner;
		$this->taskQueue = new SplQueue();
		$this->runningTask = null;
		$this->limit = $limit;
	}


	public function process(Task $task): PromiseInterface
	{
		return new Promise(function (callable $resolve) use ($task): void {
			if ($this->runningCount === $this->limit) {
				$this->taskQueue->enqueue([$task, $resolve]);

			} else {
				$this->innerProcess($task, $resolve);
			}
		});
	}


	private function innerProcess(Task $task, callable $resolve): void
	{
//		dump('IP');
		$this->runningTask = $task;
		$this->runningCount++;

//		dump('IPP');

		$this->inner->process($task)->then(
			function ($result) use ($resolve) {
//				dump('inner process done');
				$resolve($result);
//				dump('after resolve');

				$this->runningCount--;
				$this->runningTask = null;

				if (!$this->taskQueue->isEmpty()) {
//					dump('not-empty');
					[$nextTask, $nextTaskResolve] = $this->taskQueue->dequeue();
//					dump('not-empty.p');
					$this->innerProcess($nextTask, $nextTaskResolve);
//					dump('not-empty.Q');

				} else {
//					dump('empty');
				}

				return $result;
			},
			function ($err) {
				dump($err);
			}
		);
	}
}

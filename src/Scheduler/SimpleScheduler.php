<?php declare(strict_types = 1);

namespace ApiGen\Scheduler;

use ApiGen\Scheduler;
use ApiGen\Task\Task;
use ApiGen\Task\TaskHandler;
use SplQueue;


/**
 * @template   T of Task
 * @template   R
 * @implements Scheduler<T, R>
 */
class SimpleScheduler implements Scheduler
{
	/** @var SplQueue<T>  */
	protected SplQueue $tasks;


	/**
	 * @param TaskHandler<T, R> $handler
	 */
	public function __construct(
		protected TaskHandler $handler,
	) {
		$this->tasks = new SplQueue();
	}


	public function schedule(Task $task): void
	{
		$this->tasks->enqueue($task);
	}


	public function results(): iterable
	{
		while (!$this->tasks->isEmpty()) {
			$task = $this->tasks->dequeue();
			$result = $this->handler->handle($task);
			yield $task => $result;
		}
	}
}

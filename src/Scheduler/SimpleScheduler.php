<?php declare(strict_types = 1);

namespace ApiGen\Scheduler;

use ApiGen\Scheduler;
use ApiGen\Task\Task;
use ApiGen\Task\TaskHandler;
use ApiGen\Task\TaskHandlerFactory;
use SplQueue;


/**
 * @template   TTask of Task
 * @template   TResult
 * @template   TContext
 * @implements Scheduler<TTask, TResult, TContext>
 */
class SimpleScheduler implements Scheduler
{
	/** @var SplQueue<TTask>  */
	protected SplQueue $tasks;


	/**
	 * @param TaskHandlerFactory<TContext, TaskHandler<TTask, TResult>> $handlerFactory
	 */
	public function __construct(
		protected TaskHandlerFactory $handlerFactory,
	) {
		$this->tasks = new SplQueue();
	}


	/**
	 * @param  TTask $task
	 */
	public function schedule(Task $task): void
	{
		$this->tasks->enqueue($task);
	}


	/**
	 * @param  TContext $context
	 * @return iterable<TTask, TResult>
	 */
	public function process(mixed $context): iterable
	{
		$handler = $this->handlerFactory->create($context);
		while (!$this->tasks->isEmpty()) {
			$task = $this->tasks->dequeue();
			$result = $handler->handle($task);
			yield $task => $result;
		}
	}
}

<?php declare(strict_types = 1);

namespace ApiGen;

use ApiGen\Task\Task;


/**
 * @template TTask of Task
 * @template TResult
 * @template TContext
 */
interface Scheduler
{
	/**
	 * @param  TTask $task
	 */
	public function schedule(Task $task): void;


	/**
	 * @param  TContext $context
	 * @return iterable<TTask, TResult>
	 */
	public function process(mixed $context): iterable;
}

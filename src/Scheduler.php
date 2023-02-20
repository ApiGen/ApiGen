<?php declare(strict_types = 1);

namespace ApiGen;

use ApiGen\Task\Task;


/**
 * @template T of Task
 * @template R
 */
interface Scheduler
{
	/**
	 * @param T $task
	 */
	public function schedule(Task $task): void;


	/**
	 * @return iterable<T, R>
	 */
	public function results(): iterable;
}

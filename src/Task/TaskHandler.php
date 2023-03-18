<?php declare(strict_types = 1);

namespace ApiGen\Task;


/**
 * @template T of Task
 * @template R
 */
interface TaskHandler
{
	/**
	 * @param  T $task
	 * @return R
	 */
	public function handle(Task $task): mixed;
}

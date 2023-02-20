<?php declare(strict_types = 1);

namespace ApiGen\Scheduler;

use ApiGen\Scheduler;
use ApiGen\Task\Task;
use ApiGen\Task\TaskHandler;

use function function_exists;


class SchedulerFactory
{
	/**
	 * @template T of Task
	 * @template R
	 *
	 * @param    TaskHandler<T, R> $handler
	 * @return   Scheduler<T, R>
	 */
	public static function create(TaskHandler $handler, int $workerCount): Scheduler
	{
		if (function_exists('proc_open') && $workerCount > 1) {
			return new ExecScheduler($handler::class, $workerCount);

		} else {
			return new SimpleScheduler($handler);
		}
	}
}

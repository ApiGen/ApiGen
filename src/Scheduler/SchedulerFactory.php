<?php declare(strict_types = 1);

namespace ApiGen\Scheduler;

use ApiGen\Scheduler;
use ApiGen\Task\Task;
use ApiGen\Task\TaskHandler;

use function extension_loaded;
use function function_exists;

use const PHP_OS_FAMILY;
use const PHP_SAPI;


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
		if ($workerCount > 1 && PHP_OS_FAMILY !== 'Windows' && PHP_SAPI === 'cli') {
			if (extension_loaded('pcntl')) {
				return new ForkScheduler($handler, $workerCount);

			} elseif (function_exists('proc_open')) {
				return new ExecScheduler($handler::class, $workerCount);
			}
		}

		return new SimpleScheduler($handler);
	}
}

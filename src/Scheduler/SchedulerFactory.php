<?php declare(strict_types = 1);

namespace ApiGen\Scheduler;

use ApiGen\Scheduler;
use ApiGen\Task\Task;
use ApiGen\Task\TaskHandler;
use ApiGen\Task\TaskHandlerFactory;
use Nette\DI\Container;

use function extension_loaded;
use function function_exists;

use const PHP_OS_FAMILY;
use const PHP_SAPI;


class SchedulerFactory
{
	/**
	 * @template TTask of Task
	 * @template TResult
	 * @template TContext
	 *
	 * @param    class-string<TaskHandlerFactory<TContext, TaskHandler<TTask, TResult>>> $handlerFactoryType
	 * @return   Scheduler<TTask, TResult, TContext>
	 */
	public static function create(Container $container, string $handlerFactoryType, int $workerCount): Scheduler
	{
		if ($workerCount > 1 && PHP_OS_FAMILY !== 'Windows' && PHP_SAPI === 'cli') {
			if (extension_loaded('pcntl')) {
				$handlerFactory = $container->getByType($handlerFactoryType) ?? throw new \LogicException();
				return new ForkScheduler($handlerFactory, $workerCount);

			} elseif (function_exists('proc_open')) {
				return new ExecScheduler($container::class, $handlerFactoryType, $workerCount);
			}
		}

		$handlerFactory = $container->getByType($handlerFactoryType) ?? throw new \LogicException();
		return new SimpleScheduler($handlerFactory);
	}
}

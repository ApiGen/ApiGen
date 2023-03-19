<?php declare(strict_types = 1);

namespace ApiGen\Scheduler;

use ApiGen\Bootstrap;
use ApiGen\Task\Task;
use ApiGen\Task\TaskHandler;
use ApiGen\Task\TaskHandlerFactory;
use Nette\DI\Container;


if (count($argv) !== 5) {
	throw new \RuntimeException('Invalid number of arguments.');
}

/** @var string $autoloadPath */
$autoloadPath = $argv[1];

/** @var string $containerClassPath */
$containerClassPath = $argv[2];

/** @var class-string<Container> $containerClassName */
$containerClassName = $argv[3];

/** @var class-string<TaskHandlerFactory<mixed, TaskHandler<Task, mixed>>> $handlerFactoryClassName */
$handlerFactoryClassName = $argv[4];

require $autoloadPath;
Bootstrap::configureErrorHandling();

require $containerClassPath;
$container = new $containerClassName;

$context = WorkerScheduler::readMessage(STDIN);
$handlerFactory = $container->getByType($handlerFactoryClassName) ?? throw new \LogicException();
$handler = $handlerFactory->create($context);
WorkerScheduler::workerLoop($handler, STDIN, STDOUT);

<?php declare(strict_types = 1);

namespace ApiGen\Scheduler;

use ApiGen\Bootstrap;
use ApiGen\Task\Task;
use ApiGen\Task\TaskHandler;
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

/** @var class-string<TaskHandler<Task, mixed>> $handlerClassName */
$handlerClassName = $argv[4];

require $autoloadPath;
Bootstrap::configureErrorHandling();

require $containerClassPath;
$container = new $containerClassName;

/** @var TaskHandler<Task, mixed> $handler */
$handler = $container->getByType($handlerClassName) ?? throw new \LogicException();

/** @var Task $task */
while (($task = ExecScheduler::readMessage(STDIN)) !== null) {
	$result = $handler->handle($task);
	ExecScheduler::writeMessage(STDOUT, [$task, $result]);
}

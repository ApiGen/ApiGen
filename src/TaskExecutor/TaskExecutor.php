<?php declare(strict_types = 1);

namespace ApiGenX\TaskExecutor;

use React\Promise\PromiseInterface;


interface TaskExecutor
{
	public function runningCount(): int;

	public function process(Task $task): PromiseInterface;
}

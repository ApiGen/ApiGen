<?php declare(strict_types = 1);

namespace ApiGenX\TaskExecutor;

use React\Promise\Promise;
use React\Promise\PromiseInterface;


final class SimpleTaskExecutor implements TaskExecutor
{
	private TaskEnvironment $env;


	public function __construct(TaskEnvironment $env)
	{
		$this->env = $env;
	}


	public function runningCount(): int
	{
		return 0;
	}


	public function process(Task $task): PromiseInterface
	{
		return new Promise(function (callable $resolve) use ($task): void {
			$resolve($task->run($this->env));
		});
	}
}

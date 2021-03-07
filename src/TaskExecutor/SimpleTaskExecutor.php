<?php declare(strict_types = 1);

namespace ApiGenX\TaskExecutor;

use React\EventLoop\LoopInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;


final class SimpleTaskExecutor implements TaskExecutor
{
	public function __construct(
		private LoopInterface $loop,
		private TaskEnvironment $env,
	) {
	}


	public function runningCount(): int
	{
		return 0;
	}


	public function process(Task $task): PromiseInterface
	{
		return new Promise(function (callable $resolve) use ($task): void {
			$this->loop->futureTick(function () use ($resolve, $task) {
				$resolve($task->run($this->env));
			});
		});
	}
}

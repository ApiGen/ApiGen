<?php declare(strict_types = 1);

namespace ApiGenX\TaskExecutor;

use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use SplQueue;


final class WorkerTaskExecutor implements TaskExecutor
{
	private LoopInterface $loop;

	private SplQueue $queue;

	private ?MessageStream $messageStream;


	public function __construct(LoopInterface $loop)
	{
		$this->loop = $loop;
		$this->queue = new SplQueue();
		$this->messageStream = null;
	}


	public function runningCount(): int
	{
		return $this->queue->count();
	}


	public function process(Task $task): PromiseInterface
	{
		if ($this->messageStream === null) {
			$process = new Process(sprintf('%s %s', escapeshellarg(PHP_BINARY), escapeshellarg(__DIR__ . '/worker.php')));
			$process->start($this->loop);

			$this->messageStream = new MessageStream($process->stdout, $process->stdin);
			$this->messageStream->on('data', function (array $result) use ($process) {
				$resolve = $this->queue->dequeue();
				$resolve($result);

				if ($this->queue->isEmpty()) {
					$this->messageStream = null;
					$process->stdin->close();
					$process->stdout->close();
					$process->stderr->close();
					$process->terminate();
				}
			});
		}

		return new Promise(function (callable $resolve, callable $reject) use ($task) {
			$this->queue->push($resolve);
			$this->messageStream->write($task);
		});
	}
}

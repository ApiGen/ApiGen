<?php declare(strict_types = 1);

namespace ApiGen\Scheduler;

use ApiGen\Bootstrap;
use ApiGen\Helpers;
use ApiGen\Scheduler;
use ApiGen\Task\Task;
use ApiGen\Task\TaskHandler;
use Composer\Autoload\ClassLoader;
use SplQueue;

use function array_column;
use function array_fill_keys;
use function array_key_first;
use function array_keys;
use function base64_decode;
use function base64_encode;
use function count;
use function dirname;
use function fwrite;
use function proc_close;
use function proc_open;
use function serialize;
use function stream_get_line;
use function stream_select;
use function strlen;
use function unserialize;

use const PHP_BINARY;
use const STDERR;


/**
 * @template   T of Task
 * @template   R
 * @implements Scheduler<T, R>
 */
class ExecScheduler implements Scheduler
{
	protected const WORKER_CAPACITY_LIMIT = 8;

	/** @var SplQueue<T> queue of tasks which needs to be sent to workers */
	protected SplQueue $tasks;

	/** @var int total number of pending tasks (including those already sent to workers) */
	protected int $pendingTaskCount = 0;

	/** @var resource[] $workers indexed by [] */
	protected array $workers = [];

	/** @var array<array{resource, resource}> indexed by [] */
	protected array $workerPipes = [];


	/**
	 * @param class-string<TaskHandler<T, R>> $handlerClass
	 */
	public function __construct(
		protected string $handlerClass,
		protected int $workerCount,
	) {
		$this->tasks = new SplQueue();
	}


	/**
	 * @param resource $resource
	 */
	public static function writeMessage($resource, mixed $message): void
	{
		$line = base64_encode(serialize($message)) . "\n";

		if (fwrite($resource, $line) !== strlen($line)) {
			throw new \RuntimeException('Failed to write message to pipe.');
		}
	}


	/**
	 * @param resource $resource
	 */
	public static function readMessage($resource): mixed
	{
		$line = stream_get_line($resource, 128 * 1024 * 1024, "\n");

		if ($line === false) {
			return null;
		}

		return unserialize(base64_decode($line));
	}


	public function schedule(Task $task): void
	{
		$this->tasks->enqueue($task);
		$this->pendingTaskCount++;
	}


	public function results(): iterable
	{
		try {
			$this->start();

			$allWritablePipes = array_column($this->workerPipes, 0);
			$allReadablePipes = array_column($this->workerPipes, 1);
			$idleWorkers = array_fill_keys(array_keys($this->workers), self::WORKER_CAPACITY_LIMIT);

			while ($this->pendingTaskCount > 0) {
				while (count($idleWorkers) > 0 && !$this->tasks->isEmpty()) {
					$idleWorkerId = array_key_first($idleWorkers);
					$idleWorkerCapacity = $idleWorkers[$idleWorkerId];
					self::writeMessage($allWritablePipes[$idleWorkerId], $this->tasks->dequeue());
					unset($idleWorkers[$idleWorkerId]);

					if ($idleWorkerCapacity > 1) {
						$idleWorkers[$idleWorkerId] = $idleWorkerCapacity - 1;
					}
				}

				$readable = $allReadablePipes;
				$writable = null;
				$except = null;
				$changedCount = stream_select($readable, $writable, $except, null);

				if ($changedCount === false || $changedCount === 0) {
					throw new \RuntimeException('stream_select() failed.');
				}

				foreach ($readable as $workerId => $pipe) {
					[$task, $result] = self::readMessage($pipe) ?? throw new \RuntimeException('Failed to read message from worker.');
					$idleWorkers[$workerId] = ($idleWorkers[$workerId] ?? 0) + 1;
					$this->pendingTaskCount--;
					yield $task => $result;
				}
			}

		} finally {
			$this->stop();
		}
	}


	protected function start(): void
	{
		$workerCommand = [
			PHP_BINARY,
			__DIR__ . '/worker.php',
			dirname(Helpers::classLikePath(ClassLoader::class), 2) . '/autoload.php',
			Helpers::classLikePath(Bootstrap::$containerClassName),
			Bootstrap::$containerClassName,
			$this->handlerClass,
		];

		for ($i = 0; $i < $this->workerCount; $i++) {
			$workerProcess = proc_open(
				$workerCommand,
				[['pipe', 'r'], ['pipe', 'w'], STDERR],
				$this->workerPipes[$i],
				options: ['bypass_shell' => true],
			);

			if ($workerProcess === false) {
				throw new \RuntimeException('Failed to start worker process, try running ApiGen with --workers 1');
			}

			$this->workers[$i] = $workerProcess;
		}
	}


	protected function stop(): void
	{
		$this->pendingTaskCount = 0;

		foreach ($this->workers as $worker) {
			if (proc_close($worker) !== 0) {
				throw new \RuntimeException('Worker process crashed, try running ApiGen with --workers 1');
			}
		}
	}
}

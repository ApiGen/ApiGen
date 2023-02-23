<?php declare(strict_types = 1);

namespace ApiGen\Scheduler;

use ApiGen\Scheduler;
use ApiGen\Task\Task;
use ApiGen\Task\TaskHandler;
use SplQueue;

use function array_fill_keys;
use function array_key_first;
use function array_keys;
use function base64_decode;
use function base64_encode;
use function count;
use function fwrite;
use function serialize;
use function stream_get_line;
use function stream_select;
use function strlen;
use function unserialize;


/**
 * @template   T of Task
 * @template   R
 * @implements Scheduler<T, R>
 */
abstract class WorkerScheduler implements Scheduler
{
	protected const WORKER_CAPACITY_LIMIT = 8;

	/** @var SplQueue<T> queue of tasks which needs to be sent to workers */
	protected SplQueue $tasks;

	/** @var int total number of pending tasks (including those already sent to workers) */
	protected int $pendingTaskCount = 0;

	/** @var resource[] indexed by [workerId] */
	protected array $workerReadableStreams = [];

	/** @var resource[] indexed by [workerId] */
	protected array $workerWritableStreams = [];


	public function __construct(
		protected int $workerCount,
	) {
		$this->tasks = new SplQueue();
	}


	/**
	 * @param resource $stream
	 */
	public static function writeMessage($stream, mixed $message): void
	{
		$line = base64_encode(serialize($message)) . "\n";

		if (fwrite($stream, $line) !== strlen($line)) {
			throw new \RuntimeException('Failed to write message to stream.');
		}
	}


	/**
	 * @param resource $stream
	 */
	public static function readMessage($stream): mixed
	{
		$line = stream_get_line($stream, 128 * 1024 * 1024, "\n");

		if ($line === false) {
			return null;
		}

		return unserialize(base64_decode($line));
	}


	/**
	 * @template T2 of Task
	 * @template R2
	 *
	 * @param TaskHandler<T2, R2> $handler
	 * @param resource            $inputStream
	 * @param resource            $outputStream
	 */
	public static function workerLoop(TaskHandler $handler, $inputStream, $outputStream): void
	{
		while (($task = self::readMessage($inputStream)) !== null) {
			$result = $handler->handle($task);
			self::writeMessage($outputStream, [$task, $result]);
		}
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

			$idleWorkers = array_fill_keys(array_keys($this->workerWritableStreams), self::WORKER_CAPACITY_LIMIT);

			while ($this->pendingTaskCount > 0) {
				while (count($idleWorkers) > 0 && !$this->tasks->isEmpty()) {
					$idleWorkerId = array_key_first($idleWorkers);
					$idleWorkerCapacity = $idleWorkers[$idleWorkerId];
					self::writeMessage($this->workerWritableStreams[$idleWorkerId], $this->tasks->dequeue());
					unset($idleWorkers[$idleWorkerId]);

					if ($idleWorkerCapacity > 1) {
						$idleWorkers[$idleWorkerId] = $idleWorkerCapacity - 1;
					}
				}

				$readable = $this->workerReadableStreams;
				$writable = null;
				$except = null;
				$changedCount = stream_select($readable, $writable, $except, null);

				if ($changedCount === false || $changedCount === 0) {
					throw new \RuntimeException('stream_select() failed.');
				}

				foreach ($readable as $workerId => $stream) {
					[$task, $result] = self::readMessage($stream) ?? throw new \RuntimeException('Failed to read message from worker.');
					$idleWorkers[$workerId] = ($idleWorkers[$workerId] ?? 0) + 1;
					$this->pendingTaskCount--;
					yield $task => $result;
				}
			}

		} finally {
			$this->pendingTaskCount = 0;
			$this->stop();
		}
	}


	abstract protected function start(): void;


	abstract protected function stop(): void;
}

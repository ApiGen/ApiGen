<?php declare(strict_types = 1);

namespace ApiGen\Scheduler;

use ApiGen\Task\Task;
use ApiGen\Task\TaskHandler;
use ApiGen\Task\TaskHandlerFactory;

use function fclose;
use function pcntl_fork;
use function pcntl_waitpid;
use function pcntl_wexitstatus;
use function pcntl_wifexited;
use function pcntl_wifsignaled;
use function pcntl_wtermsig;
use function stream_socket_pair;

use const STREAM_IPPROTO_IP;
use const STREAM_PF_UNIX;
use const STREAM_SOCK_STREAM;


/**
 * @template TTask of Task
 * @template TResult
 * @template TContext
 * @extends  WorkerScheduler<TTask, TResult, TContext>
 */
class ForkScheduler extends WorkerScheduler
{
	/** @var int[] $workers indexed by [workerId] */
	protected array $workers = [];


	/**
	 * @param TaskHandlerFactory<TContext, TaskHandler<TTask, TResult>> $handlerFactory
	 */
	public function __construct(
		protected TaskHandlerFactory $handlerFactory,
		int $workerCount,
	) {
		parent::__construct($workerCount);
	}


	/**
	 * @param  TContext $context
	 */
	protected function start(mixed $context): void
	{
		$handler = $this->handlerFactory->create($context);

		for ($workerId = 0; $workerId < $this->workerCount; $workerId++) {
			$toWorker = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
			$toMaster = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);

			if ($toWorker === false || $toMaster === false) {
				throw new \RuntimeException('Failed to create socket pair, try running ApiGen with --workers 1');
			}

			[$masterOutput, $workerInput] = $toWorker;
			[$workerOutput, $masterInput] = $toMaster;

			$pid = pcntl_fork();

			if ($pid < 0) {
				throw new \RuntimeException('Failed to fork process, try running ApiGen with --workers 1');

			} elseif ($pid === 0) {
				fclose($masterInput);
				fclose($masterOutput);
				self::workerLoop($handler, $workerInput, $workerOutput);
				exit(0);

			} else {
				fclose($workerInput);
				fclose($workerOutput);
				$this->workers[$workerId] = $pid;
				$this->workerReadableStreams[$workerId] = $masterInput;
				$this->workerWritableStreams[$workerId] = $masterOutput;
			}
		}
	}


	protected function stop(): void
	{
		foreach ($this->workerWritableStreams as $stream) {
			fclose($stream);
		}

		foreach ($this->workerReadableStreams as $stream) {
			fclose($stream);
		}

		foreach ($this->workers as $pid) {
			pcntl_waitpid($pid, $status);

			if (pcntl_wifexited($status)) {
				if (($exitCode = pcntl_wexitstatus($status)) !== 0) {
					throw new \RuntimeException("Worker with PID $pid exited with code $exitCode, try running ApiGen with --workers 1");
				}

			} elseif (pcntl_wifsignaled($status)) {
				$signal = pcntl_wtermsig($status);
				throw new \RuntimeException("Worker with PID $pid was killed by signal $signal, try running ApiGen with --workers 1");

			} else {
				throw new \LogicException('Invalid worker state');
			}
		}
	}
}

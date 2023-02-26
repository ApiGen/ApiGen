<?php declare(strict_types = 1);

namespace ApiGen\Scheduler;

use ApiGen\Task\Task;
use ApiGen\Task\TaskHandler;

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
 * @template T of Task
 * @template R
 * @extends  WorkerScheduler<T, R>
 */
class ForkScheduler extends WorkerScheduler
{
	/** @var int[] $workersProcessIds indexed by [] */
	protected array $workersProcessIds = [];


	/**
	 * @param TaskHandler<T, R> $handler
	 */
	public function __construct(
		protected TaskHandler $handler,
		protected int $workerCount,
	) {
		parent::__construct();
	}


	protected function start(): void
	{
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
				self::workerLoop($this->handler, $workerInput, $workerOutput);
				exit(0);

			} else {
				fclose($workerInput);
				fclose($workerOutput);
				$this->workersProcessIds[$workerId] = $pid;
				$this->workerStreams[$workerId] = [$masterOutput, $masterInput];
			}
		}
	}


	protected function stop(): void
	{
		foreach ($this->workerStreams as [$output, $input]) {
			fclose($output);
			fclose($input);
		}

		foreach ($this->workersProcessIds as $pid) {
			pcntl_waitpid($pid, $status);

			if (pcntl_wifexited($status)) {
				$exitCode = pcntl_wexitstatus($status);
				if ($exitCode !== 0) {
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

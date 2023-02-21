<?php declare(strict_types = 1);

namespace ApiGen\Scheduler;

use ApiGen\Bootstrap;
use ApiGen\Helpers;
use ApiGen\Task\Task;
use ApiGen\Task\TaskHandler;
use Composer\Autoload\ClassLoader;

use function dirname;
use function proc_close;
use function proc_open;

use const PHP_BINARY;
use const STDERR;


/**
 * @template T of Task
 * @template R
 * @extends  WorkerScheduler<T, R>
 */
class ExecScheduler extends WorkerScheduler
{
	/** @var resource[] $workers indexed by [] */
	protected array $workers = [];


	/**
	 * @param class-string<TaskHandler<T, R>> $handlerClass
	 */
	public function __construct(
		protected string $handlerClass,
		protected int $workerCount,
	) {
		parent::__construct();
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
				$this->workerStreams[$i],
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
		foreach ($this->workers as $worker) {
			if (proc_close($worker) !== 0) {
				throw new \RuntimeException('Worker process crashed, try running ApiGen with --workers 1');
			}
		}
	}
}

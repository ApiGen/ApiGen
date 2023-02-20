<?php declare(strict_types = 1);

namespace ApiGen;

use ApiGen\Analyzer\AnalyzeResult;
use ApiGen\Analyzer\AnalyzeTask;
use ApiGen\Analyzer\AnalyzeTaskHandler;
use ApiGen\Info\ClassLikeInfo;
use ApiGen\Info\ClassLikeReferenceInfo;
use ApiGen\Info\ErrorInfo;
use ApiGen\Info\ErrorKind;
use ApiGen\Info\FunctionInfo;
use ApiGen\Info\MissingInfo;
use ApiGen\Info\NameInfo;
use Symfony\Component\Console\Helper\ProgressBar;

use function count;
use function get_debug_type;
use function implode;
use function is_array;
use function is_object;
use function sprintf;


class Analyzer
{
	public function __construct(
		protected Locator $locator,
		protected AnalyzeTaskHandler $taskHandler,
	) {
	}


	/**
	 * @param string[] $files indexed by []
	 */
	public function analyze(ProgressBar $progressBar, array $files): AnalyzeResult
	{
		/** @var AnalyzeTask[] $tasks indexed by [path] */
		$tasks = [];

		/** @var ClassLikeInfo[] $classLike indexed by [classLikeName] */
		$classLike = [];

		/** @var array{ClassLikeInfo|FunctionInfo, ClassLikeReferenceInfo}[] $missing indexed by [classLikeName] */
		$missing = [];

		/** @var FunctionInfo[] $functions indexed by [functionName] */
		$functions = [];

		/** @var ErrorInfo[][] $errors indexed by [errorKind][] */
		$errors = [];

		$scheduleFile = static function (string $file, bool $primary) use (&$tasks, $progressBar): void {
			$file = Helpers::realPath($file);
			$tasks[$file] ??= new AnalyzeTask($file, $primary);
			$progressBar->setMaxSteps(count($tasks));
		};

		$scheduleDependencies = function (ClassLikeInfo | FunctionInfo $info) use (&$missing, &$classLike, $scheduleFile): void {
			foreach ($this->extractDependencies($info) as $dependency) {
				if (!isset($classLike[$dependency->fullLower]) && !isset($missing[$dependency->fullLower])) {
					$missing[$dependency->fullLower] = [$info, $dependency];
					$file = $this->locator->locate($dependency);

					if ($file !== null) {
						$scheduleFile($file, primary: false);
					}
				}
			}
		};

		foreach ($files as $file) {
			$scheduleFile($file, primary: true);
		}

		foreach ($tasks as &$task) {
			foreach ($this->taskHandler->handle($task) as $info) {
				if ($info instanceof ClassLikeInfo) {
					if (isset($classLike[$info->name->fullLower])) {
						$errors[ErrorKind::DuplicateSymbol->name][] = $this->createDuplicateSymbolError($info, $classLike[$info->name->fullLower]);

					} else {
						unset($missing[$info->name->fullLower]);
						$classLike[$info->name->fullLower] = $info;
						$scheduleDependencies($info);
					}

				} elseif ($info instanceof FunctionInfo) {
					if (isset($functions[$info->name->fullLower])) {
						$errors[ErrorKind::DuplicateSymbol->name][] = $this->createDuplicateSymbolError($info, $functions[$info->name->fullLower]);

					} else {
						$functions[$info->name->fullLower] = $info;
						$scheduleDependencies($info);
					}

				} elseif ($info instanceof ErrorInfo) {
					$errors[$info->kind->name][] = $info;

				} else {
					throw new \LogicException(sprintf('Unexpected task result %s', get_debug_type($info)));
				}
			}

			$progressBar->setMessage($task->sourceFile);
			$progressBar->advance();
		}

		foreach ($missing as [$referencedBy, $dependency]) {
			$classLike[$dependency->fullLower] = new MissingInfo(new NameInfo($dependency->full, $dependency->fullLower), $referencedBy->name);

			if ($referencedBy->primary) {
				$errors[ErrorKind::MissingSymbol->name][] = new ErrorInfo(
					ErrorKind::MissingSymbol,
					"Missing {$dependency->full}\nreferenced by {$referencedBy->name->full}",
				);
			}
		}

		return new AnalyzeResult($classLike, $functions, $errors);
	}


	/**
	 * @return ClassLikeReferenceInfo[] indexed by [classLike]
	 */
	protected function extractDependencies(object $value): array
	{
		$dependencies = [];
		$stack = [$value];
		$index = 1;

		while ($index > 0) {
			$value = $stack[--$index];

			if ($value instanceof ClassLikeReferenceInfo && $value->fullLower !== 'self' && $value->fullLower !== 'parent') {
				$dependencies[$value->fullLower] ??= $value;
			}

			foreach ((array) $value as $item) {
				if (is_array($item) || is_object($item)) {
					$stack[$index++] = $item;
				}
			}
		}

		return $dependencies;
	}


	protected function createDuplicateSymbolError(ClassLikeInfo | FunctionInfo $info, ClassLikeInfo | FunctionInfo $first): ErrorInfo
	{
		return new ErrorInfo(ErrorKind::DuplicateSymbol, implode("\n", [
			"Multiple definitions of {$info->name->full}.",
			"The first definition was found in {$first->file} on line {$first->startLine}",
			"and then another one was found in {$info->file} on line {$info->startLine}",
		]));
	}
}

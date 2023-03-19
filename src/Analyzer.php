<?php declare(strict_types = 1);

namespace ApiGen;

use ApiGen\Analyzer\AnalyzeResult;
use ApiGen\Analyzer\AnalyzeTask;
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
use function sprintf;


class Analyzer
{
	/**
	 * @param Scheduler<AnalyzeTask, array<ClassLikeInfo | FunctionInfo | ClassLikeReferenceInfo | ErrorInfo>, null> $scheduler
	 */
	public function __construct(
		protected Locator $locator,
		protected Scheduler $scheduler,
	) {
	}


	/**
	 * @param string[] $files indexed by []
	 */
	public function analyze(ProgressBar $progressBar, array $files): AnalyzeResult
	{
		/** @var true[] $scheduled indexed by [path] */
		$scheduled = [];

		/** @var ClassLikeInfo[] $classLike indexed by [classLikeName] */
		$classLike = [];

		/** @var array{ClassLikeReferenceInfo, ClassLikeInfo|FunctionInfo} $missing indexed by [classLikeName] */
		$missing = [];

		/** @var FunctionInfo[] $functions indexed by [functionName] */
		$functions = [];

		/** @var ErrorInfo[][] $errors indexed by [errorKind][] */
		$errors = [];

		/** @var ClassLikeInfo|FunctionInfo|null $prevInfo */
		$prevInfo = null;

		$scheduleFile = function (string $file, bool $primary) use (&$scheduled, $progressBar): void {
			$file = Helpers::realPath($file);

			if (!isset($scheduled[$file])) {
				$scheduled[$file] = true;
				$progressBar->setMaxSteps(count($scheduled));
				$this->scheduler->schedule(new AnalyzeTask($file, $primary));
			}
		};

		foreach ($files as $file) {
			$scheduleFile($file, primary: true);
		}

		foreach ($this->scheduler->process(context: null) as $task => $result) {
			foreach ($result as $info) {
				if ($info instanceof ClassLikeReferenceInfo) {
					if ($prevInfo !== null && !isset($classLike[$info->fullLower]) && !isset($missing[$info->fullLower])) {
						$missing[$info->fullLower] = [$info, $prevInfo];

						if (($file = $this->locator->locate($info)) !== null) {
							$scheduleFile($file, primary: false);
						}
					}

				} elseif ($info instanceof ClassLikeInfo) {
					if (isset($classLike[$info->name->fullLower])) {
						$errors[ErrorKind::DuplicateSymbol->name][] = $this->createDuplicateSymbolError($info, $classLike[$info->name->fullLower]);
						$prevInfo = null;

					} else {
						unset($missing[$info->name->fullLower]);
						$classLike[$info->name->fullLower] = $info;
						$prevInfo = $info;
					}

				} elseif ($info instanceof FunctionInfo) {
					if (isset($functions[$info->name->fullLower])) {
						$errors[ErrorKind::DuplicateSymbol->name][] = $this->createDuplicateSymbolError($info, $functions[$info->name->fullLower]);
						$prevInfo = null;

					} else {
						$functions[$info->name->fullLower] = $info;
						$prevInfo = $info;
					}

				} elseif ($info instanceof ErrorInfo) {
					$errors[$info->kind->name][] = $info;
					$prevInfo = null;

				} else {
					throw new \LogicException(sprintf('Unexpected task result %s', get_debug_type($info)));
				}
			}

			$progressBar->setMessage($task->sourceFile);
			$progressBar->advance();
		}

		foreach ($missing as [$dependency, $referencedBy]) {
			$name = new NameInfo($dependency->full, $dependency->fullLower);
			$classLike[$dependency->fullLower] = new MissingInfo($name, $referencedBy->name);

			if ($referencedBy->primary) {
				$errors[ErrorKind::MissingSymbol->name][] = new ErrorInfo(
					ErrorKind::MissingSymbol,
					"Missing {$dependency->full}\nreferenced by {$referencedBy->name->full}",
				);
			}
		}

		return new AnalyzeResult($classLike, $functions, $errors);
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

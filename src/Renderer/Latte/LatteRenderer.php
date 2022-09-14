<?php declare(strict_types = 1);

namespace ApiGen\Renderer\Latte;

use ApiGen\Index\Index;
use ApiGen\Index\NamespaceIndex;
use ApiGen\Info\ClassLikeInfo;
use ApiGen\Info\FunctionInfo;
use ApiGen\Renderer;
use ApiGen\Renderer\Filter;
use ApiGen\Renderer\Latte\Template\ClassLikeTemplate;
use ApiGen\Renderer\Latte\Template\ConfigParameters;
use ApiGen\Renderer\Latte\Template\FunctionTemplate;
use ApiGen\Renderer\Latte\Template\IndexTemplate;
use ApiGen\Renderer\Latte\Template\LayoutParameters;
use ApiGen\Renderer\Latte\Template\NamespaceTemplate;
use ApiGen\Renderer\Latte\Template\SourceTemplate;
use ApiGen\Renderer\Latte\Template\TreeTemplate;
use ApiGen\Renderer\UrlGenerator;
use Latte;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Nette\Utils\Json;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Console\Helper\ProgressBar;

use function array_column;
use function array_filter;
use function array_key_first;
use function array_keys;
use function array_map;
use function array_sum;
use function extension_loaded;
use function iterator_to_array;
use function lcfirst;
use function pcntl_fork;
use function pcntl_waitpid;
use function pcntl_wexitstatus;
use function pcntl_wifexited;
use function pcntl_wifsignaled;
use function pcntl_wtermsig;
use function sprintf;
use function substr;

use const PHP_SAPI;


class LatteRenderer implements Renderer
{
	public function __construct(
		protected Latte\Engine $latte,
		protected Filter $filter,
		protected UrlGenerator $urlGenerator,
		protected int $workerCount,
		protected string $title,
		protected string $version,
		protected string $outputDir,
	) {
	}


	public function render(ProgressBar $progressBar, Index $index): void
	{
		FileSystem::delete($this->outputDir);
		FileSystem::createDir($this->outputDir);

		$config = new ConfigParameters($this->title, $this->version);
		$assets = iterator_to_array(Finder::findFiles()->from(__DIR__ . '/Template/assets'));

		$tasks = [
			[$this->copyAsset(...), $assets],
			[$this->renderElementsJs(...), [null]],
			[$this->renderIndex(...), [null]],
			[$this->renderTree(...), $this->filter->filterTreePage() ? [null] : []],
			[$this->renderNamespace(...), array_filter($index->namespace, $this->filter->filterNamespacePage(...))],
			[$this->renderClassLike(...), array_filter($index->classLike, $this->filter->filterClassLikePage(...))],
			[$this->renderFunction(...), array_filter($index->function, $this->filter->filterFunctionPage(...))],
			[$this->renderSource(...), array_keys(array_filter($index->files, $this->filter->filterSourcePage(...)))],
		];

		$progressBar->setMaxSteps(array_sum(array_map('count', array_column($tasks, 1))));
		$this->forkLoop($progressBar, $this->createTaskIterator($index, $config, $tasks));
	}


	protected function copyAsset(Index $index, ConfigParameters $config, SplFileInfo $file): string
	{
		$assetName = $file->getFilename();
		$assetPath = $this->urlGenerator->getAssetPath($assetName);
		FileSystem::copy($file->getPathname(), "$this->outputDir/$assetPath");

		return $assetPath;
	}


	protected function renderElementsJs(Index $index, ConfigParameters $config): string
	{
		$elements = [];

		foreach ($index->namespace as $namespace) {
			if ($this->filter->filterNamespacePage($namespace)) {
				$elements['namespace'][] = [$namespace->name->full, $this->urlGenerator->getNamespaceUrl($namespace)];
			}
		}

		foreach ($index->classLike as $classLike) {
			if (!$this->filter->filterClassLikePage($classLike)) {
				continue;
			}

			$members = [];

			foreach ($classLike->constants as $constant) {
				$members['constant'][] = [$constant->name, $this->urlGenerator->getMemberAnchor($constant)];
			}

			foreach ($classLike->properties as $property) {
				$members['property'][] = [$property->name, $this->urlGenerator->getMemberAnchor($property)];
			}

			foreach ($classLike->methods as $method) {
				$members['method'][] = [$method->name, $this->urlGenerator->getMemberAnchor($method)];
			}

			$elements['classLike'][] = [$classLike->name->full, $this->urlGenerator->getClassLikeUrl($classLike), $members];
		}

		foreach ($index->function as $function) {
			if ($this->filter->filterFunctionPage($function)) {
				$elements['function'][] = [$function->name->full, $this->urlGenerator->getFunctionUrl($function)];
			}
		}

		$js = sprintf('window.ApiGen?.resolveElements(%s)', Json::encode($elements));
		$assetPath = $this->urlGenerator->getAssetPath('elements.js');
		FileSystem::write("$this->outputDir/$assetPath", $js);

		return $assetPath;
	}


	protected function renderIndex(Index $index, ConfigParameters $config): string
	{
		return $this->renderTemplate($this->urlGenerator->getIndexPath(), new IndexTemplate(
			index: $index,
			config: $config,
			layout: new LayoutParameters(activePage: 'index', activeNamespace: null, activeElement: null),
		));
	}


	protected function renderTree(Index $index, ConfigParameters $config): string
	{
		return $this->renderTemplate($this->urlGenerator->getTreePath(), new TreeTemplate(
			index: $index,
			config: $config,
			layout: new LayoutParameters(activePage: 'tree', activeNamespace: null, activeElement: null),
		));
	}


	protected function renderNamespace(Index $index, ConfigParameters $config, NamespaceIndex $info): string
	{
		return $this->renderTemplate($this->urlGenerator->getNamespacePath($info), new NamespaceTemplate(
			index: $index,
			config: $config,
			layout: new LayoutParameters('namespace', $info, activeElement: null),
			namespace: $info,
		));
	}


	protected function renderClassLike(Index $index, ConfigParameters $config, ClassLikeInfo $info): string
	{
		$activeNamespace = $index->namespace[$info->name->namespaceLower];

		return $this->renderTemplate($this->urlGenerator->getClassLikePath($info), new ClassLikeTemplate(
			index: $index,
			config: $config,
			layout: new LayoutParameters('classLike', $activeNamespace, $info),
			classLike: $info,
		));
	}


	protected function renderFunction(Index $index, ConfigParameters $config, FunctionInfo $info): string
	{
		$activeNamespace = $index->namespace[$info->name->namespaceLower];

		return $this->renderTemplate($this->urlGenerator->getFunctionPath($info), new FunctionTemplate(
			index: $index,
			config: $config,
			layout: new LayoutParameters('function', $activeNamespace, $info),
			function: $info,
		));
	}


	protected function renderSource(Index $index, ConfigParameters $config, string $path): string
	{
		$file = $index->files[$path];
		$activeElement = $file->classLike[array_key_first($file->classLike)] ?? $file->function[array_key_first($file->function)] ?? null;
		$activeNamespace = $activeElement ? $index->namespace[$activeElement->name->namespaceLower] : null;

		return $this->renderTemplate($this->urlGenerator->getSourcePath($path), new SourceTemplate(
			index: $index,
			config: $config,
			layout: new LayoutParameters('source', $activeNamespace, $activeElement),
			path: $path,
		));
	}


	protected function renderTemplate(string $outputPath, object $template): string
	{
		$className = (new ReflectionClass($template))->getShortName();
		$lattePath = 'pages/' . lcfirst(substr($className, 0, -8)) . '.latte';
		FileSystem::write("$this->outputDir/$outputPath", $this->latte->renderToString($lattePath, $template));

		return $outputPath;
	}


	/**
	 * @param  array<array{callable(Index, ConfigParameters, mixed): string, array}> $taskSets
	 * @return iterable<callable(): string>
	 */
	protected function createTaskIterator(Index $index, ConfigParameters $config, array $taskSets): iterable
	{
		foreach ($taskSets as [$renderer, $tasks]) {
			foreach ($tasks as $task) {
				yield fn() => $renderer($index, $config, $task);
			}
		}
	}


	/**
	 * @param iterable<callable(): string> $it
	 */
	protected function forkLoop(ProgressBar $progressBar, iterable $it): void
	{
		$workerCount = PHP_SAPI === 'cli' && extension_loaded('pcntl') ? $this->workerCount : 1;

		$workers = [];
		$workerId = 0;

		for ($i = 1; $i < $workerCount; $i++) {
			$pid = pcntl_fork();

			if ($pid < 0) {
				throw new \RuntimeException('Failed to fork process, try running ApiGen with --workers 1');

			} elseif ($pid === 0) {
				$workerId = $i;
				$progressBar = null;
				break;

			} else {
				$workers[] = $pid;
			}
		}

		$index = 0;
		foreach ($it as $handle) {
			if ((($index++) % $workerCount) === $workerId) {
				$message = $handle();
				$progressBar?->setMessage($message);
			}

			$progressBar?->advance();
		}

		if ($workerId !== 0) {
			exit;
		}

		foreach ($workers as $pid) {
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

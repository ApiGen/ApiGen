<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Index\FileInfo;
use ApiGenX\Index\Index;
use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Templates\Classic\ClassTemplate;
use ApiGenX\Templates\Classic\NamespaceTemplate;
use ApiGenX\Templates\Classic\SourceTemplate;
use ApiGenX\Templates\Classic\TreeTemplate;
use Latte;
use League\CommonMark\CommonMarkConverter;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PhpParser\PrettyPrinter\Standard;


final class Renderer
{
	/** @var UrlGenerator */
	private UrlGenerator $url;

	/** @var CommonMarkConverter */
	private CommonMarkConverter $commonMark;

	/** @var SourceHighlighter */
	private SourceHighlighter $sourceHighlighter;

	/** @var Latte\Engine */
	private Latte\Engine $latte;


	public function __construct(UrlGenerator $urlGenerator, CommonMarkConverter $commonMark, SourceHighlighter $sourceHighlighter)
	{
		$this->url = $urlGenerator;
		$this->commonMark = $commonMark;
		$this->sourceHighlighter = $sourceHighlighter;
		$this->latte = $this->createLatte();
	}


	public function render(Index $index, string $outputDir, int $workerCount = 1)
	{
		$template = new TreeTemplate();
		$template->index = $index;

		$this->renderTemplate($template, "$outputDir/{$this->url->tree()}");

		$this->forkLoop($workerCount, $index->namespace, function (NamespaceIndex $info) use ($outputDir, $index) {
			$template = new NamespaceTemplate();
			$template->index = $index;
			$template->layoutNamespace = $info;
			$template->layoutClassLike = null;

			$template->namespace = $info;

			$this->renderTemplate($template, "$outputDir/{$this->url->namespace($info)}");
		});

		$this->forkLoop($workerCount, $index->classLike, function (ClassLikeInfo $info) use ($outputDir, $index) {
			$template = new ClassTemplate();
			$template->index = $index;
			$template->layoutNamespace = $index->namespace[$info->name->namespaceLower];
			$template->layoutClassLike = $info;

			$template->class = $info;

			$this->renderTemplate($template, "$outputDir/{$this->url->classLike($info)}");
		});

		$this->forkLoop($workerCount, $index->files, function (FileInfo $info, $path) use ($outputDir, $index) {
			if (!$info->primary) {
				return;
			}

			$template = new SourceTemplate();
			$template->index = $index;
			$template->layoutNamespace = null;
			$template->layoutClassLike = null;

			$template->fileName = $path;
			$template->source = $this->sourceHighlighter->highlight($path);

			$this->renderTemplate($template, "$outputDir/{$this->url->source($path)}");
		});
	}


	private function renderTemplate(object $template, string $outputPath): void
	{
		$classPath = (new \ReflectionClass($template))->getFileName();
		$lattePath = dirname($classPath) . '/' . lcfirst(basename($classPath, 'Template.php')) . '.latte';
		FileSystem::write($outputPath, $this->latte->renderToString($lattePath, $template));
	}


	private function forkLoop(int $workerCount, iterable $it, callable $handle)
	{
		$workers = [];
		$workerId = 0;

		for ($i = 1; $i < $workerCount; $i++) {
			$pid = pcntl_fork();

			if ($pid < 0) {
				throw new \RuntimeException();

			} elseif ($pid === 0) {
				$workerId = $i;
				break;

			} else {
				$workers[] = $pid;
			}
		}

		$index = 0;
		foreach ($it as $key => $value) {
			if ((($index++) % $workerCount) === $workerId) {
				$handle($value, $key);
			}
		}

		if ($workerId !== 0) {
			exit;
		}

		foreach ($workers as $pid) {
			pcntl_waitpid($pid, $status);
		}
	}


	private function createLatte(): Latte\Engine
	{
		$exprPrinter = new Standard();

		$latte = new Latte\Engine();
		$latte->setTempDirectory(__DIR__ . '/../temp');

		$latte->addFilter('staticFile', fn(string $file) => "/src/Templates/Classic/$file"); // TODO!
		$latte->addFilter('relativePath', fn(?string $path) => $path ? $this->url->relative($path) : null); // TODO!
		$latte->addFilter('shortDescription', fn(string $description) => Strings::before("$description\n", "\n")); // TODO!
		$latte->addFilter('longDescription', fn(string $description) => new Latte\Runtime\Html($this->commonMark->convertToHtml($description)));
		$latte->addFilter('groupUrl', fn(string $s) => $s);
		$latte->addFilter('namespaceUrl', [$this->url, 'namespace']);
		$latte->addFilter('elementUrl', [$this->url, 'classLike']); // TODO: rename
		$latte->addFilter('sourceUrl', [$this->url, 'source']);
		$latte->addFilter('exprPrint', [$exprPrinter, 'prettyPrintExpr']);

		$latte->addFunction('stripHtml', fn (Latte\Runtime\Html $html) => html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8')); // TODO!

		return $latte;
	}
}

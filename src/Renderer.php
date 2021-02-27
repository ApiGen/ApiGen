<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Index\Index;
use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Renderer\LatteFunctions;
use ApiGenX\Templates\ClassicX\ClassLikeTemplate;
use ApiGenX\Templates\ClassicX\GlobalParameters;
use ApiGenX\Templates\ClassicX\NamespaceTemplate;
use ApiGenX\Templates\ClassicX\TreeTemplate;
use Latte;
use League\CommonMark\CommonMarkConverter;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PhpParser\PrettyPrinter\Standard;


final class Renderer
{
	private Latte\Engine $latte;


	public function __construct(
		private UrlGenerator $url,
		private CommonMarkConverter $commonMark,
		private SourceHighlighter $sourceHighlighter
	) {
		$this->latte = $this->createLatte(); // TODO: extract
	}


	public function render(Index $index, string $outputDir, int $workerCount = 1)
	{
		$templateDir = __DIR__ . '/Templates/ClassicX';
		FileSystem::delete($outputDir);
		FileSystem::createDir($outputDir);
		FileSystem::copy("$templateDir/assets", "$outputDir/assets");

		$template = new TreeTemplate(
			global: new GlobalParameters(
				index: $index,
				activePage: 'tree',
				activeNamespace: null,
				activeClassLike: null,
			),
		);

		$this->renderTemplate($template, "$outputDir/{$this->url->tree()}");

		$this->forkLoop($workerCount, $index->namespace, function (NamespaceIndex $info) use ($outputDir, $index) {
			$template = new NamespaceTemplate(
				global: new GlobalParameters(
					index: $index,
					activePage: 'namespace',
					activeNamespace: $info,
					activeClassLike: null,
				),
				namespace: $info,
			);

			$this->renderTemplate($template, "$outputDir/{$this->url->namespace($info)}");
		});

		$this->forkLoop($workerCount, $index->classLike, function (ClassLikeInfo $info) use ($outputDir, $index) {
			$template = new ClassLikeTemplate(
					global: new GlobalParameters(
					index: $index,
					activePage: 'namespace',
					activeNamespace: $index->namespace[$info->name->namespaceLower],
					activeClassLike: $info,
				),
				classLike: $info,
			);

			$this->renderTemplate($template, "$outputDir/{$this->url->classLike($info)}");
		});
//
//		$this->forkLoop($workerCount, $index->files, function (FileInfo $info, $path) use ($outputDir, $index) {
//			if (!$info->primary) {
//				return;
//			}
//
//			$template = new SourceTemplate();
//			$template->index = $index;
//			$template->layoutNamespace = null;
//			$template->layoutClassLike = null;
//
//			$template->fileName = $path;
//			$template->source = $this->sourceHighlighter->highlight($path);
//
//			$this->renderTemplate($template, "$outputDir/{$this->url->source($path)}");
//		});
	}


	private function renderTemplate(object $template, string $outputPath): void
	{
		$classPath = (new \ReflectionClass($template))->getFileName();
		$lattePath = dirname($classPath) . '/' . basename($classPath, 'Template.php') . '.latte';
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
		$functions = new LatteFunctions();

		$latte = new Latte\Engine();
		$latte->setTempDirectory(__DIR__ . '/../temp');

		$latte->addFunction('asset', [$functions, 'asset']);
		$latte->addFunction('shortDescription', [$functions, 'shortDescription']);
		$latte->addFunction('elementName', [$functions, 'elementName']);
		$latte->addFunction('elementShortDescription', [$functions, 'elementShortDescription']);

		$latte->addFunction('namespaceUrl', [$this->url, 'namespace']);
		$latte->addFunction('elementUrl', [$this->url, 'element']); // TODO: rename?



//		$latte->addFilter('staticFile', fn(string $file) => "/src/Templates/Classic/$file"); // TODO!
//		$latte->addFilter('relativePath', fn(?string $path) => $path ? $this->url->relative($path) : null); // TODO!
		$latte->addFunction('longDescription', fn(string $description) => new Latte\Runtime\Html($this->commonMark->convertToHtml($description)));
//		$latte->addFilter('groupUrl', fn(string $s) => $s);
//		$latte->addFilter('namespaceUrl', [$this->url, 'namespace']);
//		$latte->addFilter('elementUrl', [$this->url, 'classLike']); // TODO: rename
//		$latte->addFilter('sourceUrl', [$this->url, 'source']);
//		$latte->addFilter('exprPrint', [$exprPrinter, 'prettyPrintExpr']);
//
//		$latte->addFunction('stripHtml', fn (Latte\Runtime\Html $html) => html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8')); // TODO!

		return $latte;
	}
}

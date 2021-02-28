<?php declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

ini_set('zend.exception_ignore_args', '0');

Tracy\Debugger::$strictMode = true;
Tracy\Debugger::$maxDepth = 10;
Tracy\Debugger::enable(Tracy\Debugger::DEVELOPMENT);
Latte\Bridges\Tracy\BlueScreenPanel::initialize(Tracy\Debugger::getBlueScreen());

// INPUT
$rootDir = __DIR__ . '/../../hranipex';
$sourceDirs = ['src'];
$tempDir = __DIR__ . '/../temp';
$outputDir = __DIR__ . '/../zz';
$workerCount = 16;


// INIT
$files = [];
foreach ($sourceDirs as $sourceDir) {
	$files = array_merge($files, array_keys(iterator_to_array(Nette\Utils\Finder::findFiles('*.php')->from("$rootDir/$sourceDir"))));
}


// AUTOLOADER
$stubsMap = array_map(fn (string $path) => __DIR__ . "/../vendor/phpstan/php-8-stubs/$path", PHPStan\Php8StubsMap::CLASSES);
$stubsMap['runtimeexception'] = __DIR__ . '/../stubs/@fix.php';
$stubsMap['logicexception'] = __DIR__ . '/../stubs/@fix.php';
$stubsMap['stdClass'] = __DIR__ . '/../stubs/@fix.php';

/** @var \Composer\Autoload\ClassLoader $composerAutoloader */
$composerAutoloader = require "$rootDir/vendor/autoload.php";
$composerAutoloader->unregister();
//$composerAutoloader->addClassMap($stubsMap);

$autoloader = function (string $classLikeName) use ($composerAutoloader, $stubsMap): ?string {
	return $composerAutoloader->findFile($classLikeName) ?: $stubsMap[strtolower($classLikeName)] ?? null;
};


// BASE DIR
$baseDir = realpath($rootDir . '/' . Nette\Utils\Strings::findPrefix(array_map(fn($s) => "$s/", $sourceDirs)));


// COROUTINES
$coroutineX = function (React\EventLoop\LoopInterface $loop, Generator $gen, callable $resolve, callable $reject) use (&$coroutineX) {
	$onResolve = function ($result) use ($loop, $gen, $resolve, $reject, $coroutineX) {
		$gen->send($result);
		$loop->futureTick(fn() => $coroutineX($loop, $gen, $resolve, $reject));
	};

	$onReject = function (Throwable $exception) use ($loop, $gen, $resolve, $reject, $coroutineX) {
		$gen->throw($exception);
		$loop->futureTick(fn() => $coroutineX($loop, $gen, $resolve, $reject));
	};

	$value = $gen->current();

	if ($value instanceof React\Promise\PromiseInterface) {
		$value->then($onResolve, $onReject)->then(null, $reject);

	} elseif ($value instanceof Generator) {
		$coroutineX($loop, $value, $onResolve, $onReject);

	} else {
		$resolve($gen->getReturn());
	}
};

$coroutineY = function (React\EventLoop\LoopInterface $loop, Generator $gen) use ($coroutineX) {
	return new React\Promise\Promise(function (callable $resolve, callable $reject) use ($loop, $gen, $coroutineX) {
		$loop->futureTick(fn() => $coroutineX($loop, $gen, $resolve, $reject));
	});
};


// COMPOSITION ROOT
$commonMarkEnv = League\CommonMark\Environment::createCommonMarkEnvironment();
$commonMarkEnv->addExtension(new League\CommonMark\Extension\Autolink\AutolinkExtension());
$commonMark = new League\CommonMark\CommonMarkConverter([], $commonMarkEnv);

$urlGenerator = new ApiGenX\UrlGenerator($baseDir);

$sourceHighlighter = new ApiGenX\SourceHighlighter();

$latteFunctions = new ApiGenX\Renderer\LatteFunctions();
$latteFactory = new ApiGenX\Renderer\LatteEngineFactory($latteFunctions, $urlGenerator, $commonMark, $sourceHighlighter);
$latte = $latteFactory->create($tempDir);

$loop = React\EventLoop\Factory::create();
$executor = new ApiGenX\TaskExecutor\LimitTaskExecutor(ApiGenX\TaskExecutor\PoolTaskExecutor::create(8, fn() => new ApiGenX\TaskExecutor\WorkerTaskExecutor($loop)), 80);
//$executor = new ApiGenX\TaskExecutor\SimpleTaskExecutor(new ApiGenX\TaskExecutor\DefaultTaskEnvironment());

$analyzer = new ApiGenX\Analyzer($loop, $executor);
$indexer = new ApiGenX\Indexer();
$renderer = new ApiGenX\Renderer($latte, $urlGenerator);

$apiGen = new ApiGenX\ApiGen($analyzer, $indexer, $renderer);
$coroutineY($loop, $apiGen->generate($files, $autoloader, $outputDir, $workerCount))->then(
	fn() => $loop->stop(),
	function (Throwable $e) use ($loop) {
		$loop->stop();
		Tracy\Debugger::exceptionHandler($e);
	},
);

$loop->run();

<?php declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

ini_set('zend.exception_ignore_args', '0');

Tracy\Debugger::enable(Tracy\Debugger::DEVELOPMENT);
Tracy\Debugger::$strictMode = true;
Tracy\Debugger::$maxDepth = 10;

// INPUT
$rootDir = __DIR__ . '/../../hranipex';
$sourceDirs = ['src'];


// INIT
$files = [];
foreach ($sourceDirs as $sourceDir) {
	$files = array_merge($files, array_keys(iterator_to_array(Nette\Utils\Finder::findFiles('*.php')->from("$rootDir/$sourceDir"))));
}

// AUTOLOADER
$autoloader = function (string $classLikeName) use ($rootDir): ?string {
	$composerAutoloader = require "$rootDir/vendor/autoload.php";
	$composerAutoloader->unregister();
	return $composerAutoloader->findFile($classLikeName) ?: null;
};

// BASE DIR
$baseDir = realpath($rootDir . '/' . Nette\Utils\Strings::findPrefix(array_map(fn($s) => "$s/", $sourceDirs)));

//$classMap = [];
//$classMap += array_change_key_case(require __DIR__ . '/../../hranipex/vendor/composer/autoload_classmap.php', CASE_LOWER);
//$classMap += array_change_key_case(require __DIR__ . '/../vendor/composer/autoload_classmap.php', CASE_LOWER);

$commonMarkEnv = League\CommonMark\Environment::createCommonMarkEnvironment();
$commonMarkEnv->addExtension(new League\CommonMark\Extension\Autolink\AutolinkExtension());
$commonMark = new League\CommonMark\CommonMarkConverter([], $commonMarkEnv);

$urlGenerator = new ApiGenX\UrlGenerator();
$urlGenerator->setBaseDir($baseDir);

$sourceHighlighter = new ApiGenX\SourceHighlighter();

$indexer = new ApiGenX\Indexer();
$renderer = new ApiGenX\Renderer($urlGenerator, $commonMark, $sourceHighlighter);

$apiGen = new ApiGenX\ApiGen($indexer, $renderer);

$time = -microtime(true);
$apiGen->analyze($files, $autoloader);
$time += microtime(true);

dump('Analyze');
dump(sprintf('  Time:         %6.0f ms', $time * 1e3));
dump(sprintf('  Memory usage: %6.0f MB', memory_get_usage() / 1e6));
dump(sprintf('  Memory peak:  %6.0f MB', memory_get_peak_usage() / 1e6));


$time = -microtime(true);
$apiGen->render(__DIR__ . '/../zz');
$time += microtime(true);

dump('Render');
dump(sprintf('  Time:         %6.0f ms', $time * 1e3));
dump(sprintf('  Memory usage: %6.0f MB', memory_get_usage() / 1e6));
dump(sprintf('  Memory peak:  %6.0f MB', memory_get_peak_usage() / 1e6));

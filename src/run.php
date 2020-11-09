<?php declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

ini_set('zend.exception_ignore_args', '0');

Tracy\Debugger::enable(Tracy\Debugger::DEVELOPMENT);
Tracy\Debugger::$strictMode = true;
Tracy\Debugger::$maxDepth = 10;

// INPUT
$rootDir = __DIR__ . '/../../hranipex';
$sourceDirs = ['src'];
$outputDir = __DIR__ . '/../zz';


// INIT
$files = [];
foreach ($sourceDirs as $sourceDir) {
	$files = array_merge($files, array_keys(iterator_to_array(Nette\Utils\Finder::findFiles('*.php')->from("$rootDir/$sourceDir"))));
}

// AUTOLOADER
$robotLoader = new Nette\Loaders\RobotLoader(); // TODO: use static map as stubs don't change
$robotLoader->setTempDirectory(__DIR__ . '/../temp');
$robotLoader->addDirectory(__DIR__ . '/../stubs');

$composerAutoloader = require "$rootDir/vendor/autoload.php";
$composerAutoloader->unregister();
$composerAutoloader->addClassMap($robotLoader->getIndexedClasses());

$autoloader = function (string $classLikeName) use ($composerAutoloader): ?string {
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

$analyzer = new ApiGenX\Analyzer();
$indexer = new ApiGenX\Indexer();
$renderer = new ApiGenX\Renderer($urlGenerator, $commonMark, $sourceHighlighter);

$apiGen = new ApiGenX\ApiGen($analyzer, $indexer, $renderer);
$apiGen->generate($files, $autoloader, $outputDir);

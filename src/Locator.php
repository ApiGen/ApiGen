<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Info\NameInfo;
use Composer\Autoload\ClassLoader;
use League;
use Nette\Utils\Finder;
use PHPStan\Php8StubsMap;
use ReflectionClass;


final class Locator
{
	private array $stubsMap;

	private ClassLoader $classLoader;


	public function __construct(string $projectDir)
	{
		$this->stubsMap = $this->createStubsMap();
		$this->classLoader = $this->createComposerClassLoader($projectDir);
	}


	private function createStubsMap(): array
	{
		$stubsDir = dirname((new ReflectionClass(Php8StubsMap::class))->getFileName());
		$stubsMap = array_map(fn(string $path) => "$stubsDir/$path", Php8StubsMap::CLASSES);

		foreach (Finder::findFiles('*.php')->in(__DIR__ . '/../stubs') as $path => $_) {
			$stubsMap[strtolower(pathinfo($path, PATHINFO_FILENAME))] = $path;
		}

		return $stubsMap;
	}


	private function createComposerClassLoader(string $projectDir): ClassLoader
	{
		$vendorDir = "$projectDir/vendor";
		$loader = new ClassLoader();

		if (is_dir($vendorDir)) {
			$loader->addClassMap(require "$vendorDir/composer/autoload_classmap.php");

			foreach (require "$vendorDir/composer/autoload_namespaces.php" as $prefix => $paths) {
				$loader->set($prefix, $paths);
			}

			foreach (require "$vendorDir/composer/autoload_psr4.php" as $prefix => $paths) {
				$loader->setPsr4($prefix, $paths);
			}

		} else {
			// TODO: emit warning
		}

		return $loader;
	}


	public function locate(NameInfo $name): ?string
	{
		return $this->classLoader->findFile($name->full) ?: $this->stubsMap[$name->fullLower] ?? null;
	}
}

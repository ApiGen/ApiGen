<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Info\NameInfo;
use Composer\Autoload\ClassLoader;
use JetBrains\PHPStormStub\PhpStormStubsMap;
use League;
use Nette\Utils\Finder;
use PHPStan\Php8StubsMap;
use Symfony\Component\Console\Style\OutputStyle;

use function dirname;
use function is_dir;
use function pathinfo;
use function strtolower;

use const PATHINFO_FILENAME;
use const PHP_VERSION_ID;


final class Locator
{
	/**
	 * @param string[] $stubsMap indexed by [classLikeName]
	 */
	public function __construct(
		private array $stubsMap,
		private ClassLoader $classLoader,
	) {
	}


	public static function create(OutputStyle $output, string $projectDir): self
	{
		return new self(
			self::createStubsMap(),
			self::createComposerClassLoader($output, $projectDir),
		);
	}


	/**
	 * @return string[] indexed by [classLikeName]
	 */
	private static function createStubsMap(): array
	{
		$stubsMap = [];

		$phpStormStubsDir = dirname(Helpers::classLikePath(PhpStormStubsMap::class));
		foreach (PhpStormStubsMap::CLASSES as $class => $path) {
			$stubsMap[strtolower($class)] = "$phpStormStubsDir/$path";
		}

		$phpStanStubsDir = dirname(Helpers::classLikePath(Php8StubsMap::class));
		foreach ((new Php8StubsMap(PHP_VERSION_ID))->classes as $class => $path) {
			$stubsMap[$class] = "$phpStanStubsDir/$path";
		}

		foreach (Finder::findFiles('*.php')->in(__DIR__ . '/../stubs') as $path => $_) {
			$stubsMap[strtolower(pathinfo($path, PATHINFO_FILENAME))] = $path;
		}

		return $stubsMap;
	}


	private static function createComposerClassLoader(OutputStyle $output, string $projectDir): ClassLoader
	{
		$vendorDir = "$projectDir/vendor";
		$loader = new ClassLoader();

		if (!is_dir($vendorDir)) {
			$output->warning("Unable to use Composer autoloader for finding dependencies because directory\n$vendorDir does not exist.");

		} else {
			$output->text("Using Composer autoloader for finding dependencies in $vendorDir.\n");
			$loader->addClassMap(require "$vendorDir/composer/autoload_classmap.php");

			foreach (require "$vendorDir/composer/autoload_namespaces.php" as $prefix => $paths) {
				$loader->set($prefix, $paths);
			}

			foreach (require "$vendorDir/composer/autoload_psr4.php" as $prefix => $paths) {
				$loader->setPsr4($prefix, $paths);
			}
		}

		return $loader;
	}


	public function locate(NameInfo $name): ?string
	{
		return $this->classLoader->findFile($name->full) ?: $this->stubsMap[$name->fullLower] ?? null;
	}
}

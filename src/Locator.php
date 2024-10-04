<?php declare(strict_types = 1);

namespace ApiGen;

use ApiGen\Info\ClassLikeReferenceInfo;
use Composer\Autoload\ClassLoader;
use JetBrains\PHPStormStub\PhpStormStubsMap;
use League;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use PHPStan\Php8StubsMap;
use Symfony\Component\Console\Style\OutputStyle;

use function dirname;
use function implode;
use function is_dir;
use function is_file;
use function strtolower;

use const PHP_VERSION_ID;


class Locator
{
	/**
	 * @param string[] $stubsMap indexed by [classLikeName]
	 */
	public function __construct(
		protected array $stubsMap,
		protected ClassLoader $classLoader,
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
	protected static function createStubsMap(): array
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

		return $stubsMap;
	}


	protected static function createComposerClassLoader(OutputStyle $output, string $projectDir): ClassLoader
	{
		$loader = new ClassLoader();
		$composerJsonPath = "$projectDir/composer.json";

		if (!is_file($composerJsonPath)) {
			$output->warning(implode("\n", [
				"Unable to use Composer autoloader for finding dependencies because file",
				"$composerJsonPath does not exist. Use --working-dir to specify directory where composer.json is located",
			]));

			return $loader;
		}

		$composerJson = Json::decode(FileSystem::read($composerJsonPath), forceArrays: true);
		$vendorDir = FileSystem::joinPaths($projectDir, $composerJson['config']['vendor-dir'] ?? 'vendor');

		if (!is_dir($vendorDir)) {
			$output->warning(implode("\n", [
				"Unable to use Composer autoloader for finding dependencies because directory",
				"$vendorDir does not exist. Run composer install to install dependencies.",
			]));

			return $loader;
		}

		$output->text("Using Composer autoloader for finding dependencies in $vendorDir.\n");
		$loader->addClassMap(require "$vendorDir/composer/autoload_classmap.php");

		foreach (require "$vendorDir/composer/autoload_namespaces.php" as $prefix => $paths) {
			$loader->set($prefix, $paths);
		}

		foreach (require "$vendorDir/composer/autoload_psr4.php" as $prefix => $paths) {
			$loader->setPsr4($prefix, $paths);
		}

		return $loader;
	}


	public function locate(ClassLikeReferenceInfo $name): ?string
	{
		return $this->classLoader->findFile($name->full) ?: $this->stubsMap[$name->fullLower] ?? null;
	}
}

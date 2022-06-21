<?php declare(strict_types = 1);

namespace ApiGenX;

use ErrorException;
use Nette\DI\Compiler;
use Nette\DI\Config\Loader;
use Nette\DI\ContainerLoader;
use Nette\DI\Extensions\ExtensionsExtension;
use Nette\DI\Extensions\PhpExtension;
use Nette\DI\Helpers as DIHelpers;
use Nette\Schema\Expect;
use Nette\Schema\Helpers as SchemaHelpers;
use Nette\Schema\Processor;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Style\OutputStyle;

use function array_map;
use function dirname;
use function error_reporting;
use function getcwd;
use function ini_set;
use function is_int;
use function set_error_handler;
use function str_starts_with;
use function sys_get_temp_dir;

use const E_ALL;
use const PHP_RELEASE_VERSION;
use const PHP_VERSION_ID;


final class Bootstrap
{
	public static function configureErrorHandling(): void
	{
		error_reporting(E_ALL);
		ini_set('display_errors', 'stderr');

		set_error_handler(function (int $severity, string $message, string $file, int $line) {
			if (error_reporting() & $severity) {
				throw new ErrorException($message, 0, $severity, $file, $line);
			}
		});
	}


	public static function createApiGen(OutputStyle $output, array $parameters, array $configPaths): ApiGen
	{
		$workingDir = getcwd();
		$tempDir = sys_get_temp_dir() . '/apigen';

		$config = self::mergeConfigs(
			['parameters' => ['workingDir' => $workingDir, 'tempDir' => $tempDir]],
			self::loadConfig(__DIR__ . '/../apigen.neon'),
			...array_map(self::loadConfig(...), $configPaths),
			...[['parameters' => self::resolvePaths($parameters, $workingDir)]],
		);

		self::validateParameters($config['parameters']);
		$tempDir = DIHelpers::expand($config['parameters']['tempDir'], $config['parameters']);
		$containerLoader = new ContainerLoader($tempDir, autoRebuild: true);

		$containerGenerator = function (Compiler $compiler) use ($config) {
			$compiler->addExtension('extensions', new ExtensionsExtension);
			$compiler->addExtension('php', new PhpExtension);
			$compiler->addConfig($config);
		};

		$containerKey = [
			$config,
			PHP_VERSION_ID - PHP_RELEASE_VERSION,
		];

		$containerClassName = $containerLoader->load($containerGenerator, $containerKey);

		$container = new $containerClassName();
		$container->addService('symfonyConsole.output', $output);
		$container->initialize();

		return $container->getByType(ApiGen::class);
	}


	private static function validateParameters(array $parameters): void
	{
		$schema = Expect::structure([
			// input
			'paths' => Expect::listOf('string')->min(1),
			'include' => Expect::listOf('string'),
			'exclude' => Expect::listOf('string'),

			// analysis
			'excludeProtected' => Expect::bool(),
			'excludePrivate' => Expect::bool(),
			'excludeTagged' => Expect::listOf('string'),

			// output
			'outputDir' => Expect::string(),
			'title' => Expect::string(),
			'baseUrl' => Expect::string(),

			// system
			'workingDir' => Expect::string(),
			'tempDir' => Expect::string(),
			'workerCount' => Expect::int()->min(1),
			'memoryLimit' => Expect::string(),
		]);

		(new Processor)->process($schema, $parameters);
	}


	private static function mergeConfigs(array...$configs): array
	{
		$mergedConfig = [];

		foreach ($configs as $config) {
			$mergedConfig = SchemaHelpers::merge($config, $mergedConfig);
		}

		return $mergedConfig;
	}


	private static function loadConfig(string $path): array
	{
		$data = (new Loader)->load($path);
		$data['parameters'] = self::resolvePaths($data['parameters'] ?? [], dirname($path));

		return $data;
	}


	private static function resolvePaths(array $parameters, string $base): array
	{
		foreach (['tempDir', 'workingDir', 'outputDir'] as $parameterKey) {
			if (isset($parameters[$parameterKey])) {
				$parameters[$parameterKey] = self::resolvePath($parameters[$parameterKey], $base);
			}
		}

		foreach ($parameters['paths'] ?? [] as $i => $path) {
			if (is_int($i)) {
				$parameters['paths'][$i] = self::resolvePath($parameters['paths'][$i], $base);
			}
		}

		return $parameters;
	}


	private static function resolvePath(string $path, string $base): string
	{
		return (FileSystem::isAbsolute($path) || str_starts_with($path, '%'))
			? $path
			: FileSystem::joinPaths($base, $path);
	}
}

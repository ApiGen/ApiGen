<?php declare(strict_types = 1);

namespace ApiGenX;

use ErrorException;
use Nette\DI\Compiler;
use Nette\DI\Config\Loader;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\DI\Extensions\ExtensionsExtension;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Style\OutputStyle;

use function dirname;
use function is_int;


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


	public static function createContainer(OutputStyle $output, string $tempDir, array $parameters, ?string $configPath): Container
	{
		$containerLoader = new ContainerLoader($tempDir, autoRebuild: true);

		$containerGenerator = function (Compiler $compiler) use ($parameters, $configPath) {
			$compiler->addExtension('extensions', new ExtensionsExtension);
			$compiler->loadConfig(__DIR__ . '/../apigen.neon');

			if ($configPath !== null) {
				$compiler->loadConfig($configPath, new class extends Loader {
					public function load(string $file, ?bool $merge = true): array {
						$data = parent::load($file, $merge);
						$data['parameters'] = Bootstrap::resolvePaths($data['parameters'] ?? [], dirname($file));

						return $data;
					}
				});
			}

			$compiler->addConfig(['parameters' => $parameters]);
		};

		$containerKey = [
			$parameters,
			PHP_VERSION_ID - PHP_RELEASE_VERSION,
		];

		$containerClassName = $containerLoader->load($containerGenerator, $containerKey);

		$container = new $containerClassName();
		$container->addService('symfonyConsole.output', $output);
		$container->initialize();

		return $container;
	}


	public static function resolvePaths(array $parameters, string $base): array
	{
		foreach (['tempDir', 'projectDir', 'outputDir'] as $parameterKey) {
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
		return FileSystem::isAbsolute($path) ? $path : FileSystem::joinPaths($base, $path);
	}
}

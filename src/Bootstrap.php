<?php declare(strict_types = 1);

namespace ApiGenX;

use ErrorException;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\DI\Extensions\ExtensionsExtension;
use Symfony\Component\Console\Style\OutputStyle;


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


	public static function createContainer(OutputStyle $output, string $tempDir, array $config): Container
	{
		$containerLoader = new ContainerLoader($tempDir, autoRebuild: true);

		$containerGenerator = function (Compiler $compiler) use ($config) {
			$compiler->addExtension('extensions', new ExtensionsExtension);
			$compiler->loadConfig(__DIR__ . '/../apigen.neon');
			$compiler->addConfig($config);
		};

		$containerKey = [
			$config,
			PHP_VERSION_ID - PHP_RELEASE_VERSION,
		];

		$containerClassName = $containerLoader->load($containerGenerator, $containerKey);

		/** @var Container $container */
		$container = new $containerClassName();
		$container->addService('symfonyConsole.output', $output);

		return $container;
	}
}

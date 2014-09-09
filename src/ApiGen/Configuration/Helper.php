<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use ApiGen;
use Nette;


class Helper extends Nette\Object
{
	/**
	 * The default configuration file name.
	 * @var string
	 */
	const DEFAULT_CONFIG_FILENAME = 'apigen.neon';

	/**
	 * The default template configuration file name.
	 * @var string
	 */
	const DEFAULT_TEMPLATE_CONFIG_FILENAME = 'default/config.neon';

	/**
	 * File or directory path options.
	 * @var array
	 */
	private $pathOptions = array(
		'config',
		'source',
		'destination',
		'templateConfig',
		'report',
		'exclude',
		'skipDocPath'
	);


	/**
	 * Returns default configuration file path.
	 * @return string
	 */
	public static function getDefaultConfigPath()
	{
		return getcwd() . DIRECTORY_SEPARATOR . static::DEFAULT_CONFIG_FILENAME;
	}


	/**
	 * Returns templates directory path.
	 * @return string
	 */
	public static function getTemplatesDir()
	{
		return realpath(APIGEN_ROOT_PATH . '/src/templates/');
	}


	/**
	 * Checks if default configuration file exists.
	 * @return boolean
	 */
	public static function defaultConfigExists()
	{
		return is_file(static::getDefaultConfigPath());
	}


	/**
	 * @param string $relativePath File relative path
	 * @param array $baseDirectories List of base directories
	 * @return string|NULL
	 */
	public static function getAbsolutePath($relativePath, array $baseDirectories)
	{
		if (preg_match('~/|[a-z]:~Ai', $relativePath)) { // absolute path already
			return $relativePath;
		}

		foreach ($baseDirectories as $directory) {
			$fileName = $directory . DIRECTORY_SEPARATOR . $relativePath;
			if (is_file($fileName)) {
				return realpath($fileName);
			}
		}

		return NULL;
	}


	/**
	 * @param $config
	 */
	public function sanitizeConfigOptions($config)
	{
		// Unify character sets
		$config['charset'] = array_map('strtoupper', $config['charset']);

		// Process options that specify a filesystem path
		foreach ($this->pathOptions as $option) {
			if (is_array($config[$option])) {
				array_walk($config[$option], function(&$value) {
					if (file_exists($value)) {
						$value = realpath($value);

					} else {
						$value = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $value);
					}
				});
				usort($config[$option], 'strcasecmp');

			} else {
				if (file_exists($config[$option])) {
					$config[$option] = realpath($config[$option]);

				} else {
					$config[$option] = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $config[$option]);
				}
			}
		}

		// Unify prefixes
		$config['skipDocPrefix'] = array_map(function($prefix) {
			return ltrim($prefix, '\\');
		}, $config['skipDocPrefix']);
		usort($config['skipDocPrefix'], 'strcasecmp');

		// Base url without slash at the end
		$config['baseUrl'] = rtrim($config['baseUrl'], '/');

		// Set overall DIC parameters, @todo: what for?
		$config['application'] = array(
			'name' => ApiGen\ApiGen::NAME,
			'version' => ApiGen\ApiGen::VERSION
		);

		return $config;
	}

}

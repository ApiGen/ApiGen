<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use ApiGen\Configuration\Theme\ThemeConfigFactory;
use ApiGen\Factory;
use ApiGen\FileSystem\FileSystem;
use ApiGen\Neon\NeonFile;
use Nette;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;


/**
 * @method Configuration onOptionsResolve(array $config)
 */
class Configuration extends Nette\Object
{

	/**
	 * Static access for reflections
	 *
	 * @var Nette\Utils\ArrayHash
	 */
	public static $config;

	/**
	 * @var array
	 */
	public $onOptionsResolve = [];

	/**
	 * @var ConfigurationOptionsResolver
	 */
	private $configurationOptionsResolver;


	public function __construct(ConfigurationOptionsResolver $configurationOptionsResolver)
	{
		$this->configurationOptionsResolver = $configurationOptionsResolver;
	}


	/**
	 * @return array
	 */
	public function resolveOptions(array $options)
	{
		self::$config = $options = $this->configurationOptionsResolver->resolve($options);
		$this->onOptionsResolve($options);
		return $options;
	}

}


/**
 * Thrown when an invalid configuration is detected.
 */
class ConfigurationException extends \RuntimeException
{

}

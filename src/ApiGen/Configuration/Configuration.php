<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use ApiGen\Configuration\ConfigurationOptions as CO;
use Nette;


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
	 * @var array
	 */
	private $options = [];

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
		self::$config = $this->options = $options = $this->configurationOptionsResolver->resolve($options);
		$this->onOptionsResolve($options);
		return $options;
	}


	/**
	 * @param string $name
	 * @return mixed|NULL
	 */
	public function getOption($name)
	{
		if (isset($this->getOptions()[$name])) {
			return $this->getOptions()[$name];
		}
		return NULL;
	}


	/**
	 * @return array
	 */
	public function getOptions()
	{
		if ($this->options === NULL) {
			$this->resolveOptions([]);
		}
		return $this->options;
	}


	/**
	 * @return bool
	 */
	public function isSitemapEnabled()
	{
		return ! empty($this->options[CO::BASE_URL]);
	}


	/**
	 * @return bool
	 */
	public function isOpensearchEnabled()
	{
		return ! empty($this->options[CO::GOOGLE_CSE_ID]) && ! empty($this->options[CO::BASE_URL]);
	}

}


/**
 * Thrown when an invalid configuration is detected.
 */
class ConfigurationException extends \RuntimeException
{

}

<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use Nette;


/**
 * @method array getOptions()
 * @method onOptionsResolve(object)
 */
class Configuration extends Nette\Object
{

	const GROUPS_AUTO = 'auto';
	const GROUPS_NAMESPACES = 'namespaces';
	const GROUPS_PACKAGES = 'packages';

	/**
	 * @var array
	 */
	public $onOptionsResolve = array();

	/**
	 * @var array
	 */
	private $options;

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
		$this->options = $this->configurationOptionsResolver->resolve($options);
		$this->onOptionsResolve($this);
		return $this->options;
	}


	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getOption($key)
	{
		if ( ! isset($this->options[$key])) {
			throw new ConfigurationException("Option '$key' not found");
		}
		return $this->options[$key];
	}


	/**
	 * @param int $namespaceCount
	 * @param int $packageCount
	 * @return bool
	 */
	public function areNamespacesEnabled($namespaceCount, $packageCount)
	{
		if ($this->getOption('groups') === self::GROUPS_NAMESPACES) {
			return TRUE;
		}

		if ($this->getOption('groups') === self::GROUPS_AUTO && ($namespaceCount > 0 || $packageCount === 0)) {
			return TRUE;
		}

		return FALSE;
	}


	/**
	 * @param bool $areNamespacesEnabled
	 * @return bool
	 */
	public function arePackagesEnabled($areNamespacesEnabled)
	{
		if ($this->getOption('groups') === self::GROUPS_PACKAGES) {
			return TRUE;

		} elseif ($this->getOption('groups') === self::GROUPS_AUTO && ($areNamespacesEnabled === FALSE)) {
			return TRUE;
		}

		return FALSE;
	}

}


/**
 * Thrown when an invalid configuration is detected.
 */
class ConfigurationException extends \RuntimeException
{

}

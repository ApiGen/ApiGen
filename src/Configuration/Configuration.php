<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Configuration\ParserConfigurationInterface;
use Nette;
use Nette\Utils\Strings;


/**
 * @method Configuration onOptionsResolve(array $config)
 */
class Configuration extends Nette\Object implements ConfigurationInterface, ParserConfigurationInterface
{

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
		$options = $this->unsetConsoleOptions($options);
		$this->options = $options = $this->configurationOptionsResolver->resolve($options);
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
	public function areNamespacesEnabled()
	{
		return $this->getOption(CO::GROUPS) === 'namespaces';
	}


	/**
	 * @return bool
	 */
	public function arePackagesEnabled()
	{
		return $this->getOption(CO::GROUPS) === 'packages';
	}


	/**
	 * @return string
	 */
	public function getZipFileName()
	{
		$webalizedTitle = Strings::webalize($this->getOption(CO::TITLE), NULL, FALSE);
		return ($webalizedTitle ? '-' : '') . 'API-documentation.zip';
	}


	/**
	 * {@inheritdoc}
	 */
	public function getVisibilityLevel()
	{
		return $this->options['visibilityLevels'];
	}


	/**
	 * {@inheritdoc}
	 */
	public function getMain()
	{
		return $this->getOption('main');
	}


	/**
	 * {@inheritdoc}
	 */
	public function isPhpCoreDocumented()
	{
		return (bool) $this->getOption('php');
	}


	/**
	 * {@inheritdoc}
	 */
	public function isInternalDocumented()
	{
		return (bool) $this->getOption('internal');
	}


	/**
	 * {@inheritdoc}
	 */
	public function isDeprecatedDocumented()
	{
		return (bool) $this->getOption('deprecated');
	}


	/**
	 * @return array
	 */
	private function unsetConsoleOptions(array $options)
	{
		unset($options[CO::CONFIG], $options['help'], $options['version'], $options['quiet']);
		return $options;
	}


	/**
	 * {@inheritdoc}
	 */
	public function setOptions(array $options)
	{
		$this->options = $options;
	}

}

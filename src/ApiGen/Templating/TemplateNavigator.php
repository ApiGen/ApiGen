<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Templating\Filters\SourceFilters;
use ApiGen\Templating\Filters\UrlFilters;
use Nette;
use Nette\Utils\Finder;
use RecursiveDirectoryIterator;


class TemplateNavigator extends Nette\Object
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var SourceFilters
	 */
	private $sourceFilters;

	/**
	 * @var Filters\UrlFilters
	 */
	private $urlFilters;


	public function __construct(
		Configuration $configuration,
		SourceFilters $sourceFilters,
		UrlFilters $urlFilters
	) {
		$this->configuration = $configuration;
		$this->sourceFilters = $sourceFilters;
		$this->urlFilters = $urlFilters;
	}


	/**
	 * @param string $name
	 * @return string
	 */
	public function getTemplatePath($name)
	{
		$this->prepareTemplate($name);
		$options = $this->configuration->getOptions();
		return $options[CO::TEMPLATE]['templates'][$name]['template'];
	}


	/**
	 * @param string $name
	 * @return string
	 */
	public function getTemplateFileName($name)
	{
		$options = $this->configuration->getOptions();
		return $this->getDestination() . '/' . $options[CO::TEMPLATE]['templates'][$name]['filename'];
	}


	/**
	 * @param string $namespace
	 * @return string
	 */
	public function getTemplatePathForNamespace($namespace)
	{
		return $this->getDestination() . '/' . $this->urlFilters->namespaceUrl($namespace);
	}


	/**
	 * @param string $package
	 * @return string
	 */
	public function getTemplatePathForPackage($package)
	{
		return $this->getDestination() . '/' . $this->urlFilters->packageUrl($package);
	}


	/**
	 * @return string
	 */
	public function getTemplatePathForClass(ReflectionElement $element)
	{
		return $this->getDestination() . '/' . $this->urlFilters->classUrl($element);
	}


	/**
	 * @return string
	 */
	public function getTemplatePathForConstant(ReflectionElement $element)
	{
		return $this->getDestination() . '/' . $this->urlFilters->constantUrl($element);
	}


	/**
	 * @return string
	 */
	public function getTemplatePathForFunction(ReflectionElement $element)
	{
		return $this->getDestination() . '/' . $this->urlFilters->functionUrl($element);
	}


	/**
	 * @return string
	 */
	public function getTemplatePathForSourceElement(ReflectionElement $element)
	{
		return $this->getDestination() . '/' . $this->sourceFilters->sourceUrl($element, FALSE);
	}


	public function copyResourcesToDestination()
	{
		// todo: move somewhere else?
		// theme resources prepare
		$destination = $this->configuration->getOption(CO::DESTINATION);
		$templateOptions = $this->configuration->getOptions();
		foreach ($templateOptions['template']['resources'] as $resourceSource => $resourceDestination) {
			// File
			$resourcePath = $this->getTemplateDir() . '/' . $resourceSource;
			if (is_file($resourcePath)) {
				$path = $destination . '/' . $resourceDestination;
				FileSystem::createDirForPath($path);
				copy($resourcePath, $path);
				continue;
			}

			// Dir

			/** @var RecursiveDirectoryIterator $iterator  */
			$iterator = Finder::findFiles('*')->from($resourcePath)->getIterator();
			foreach ($iterator as $item) {
				$path = $destination . '/' . $resourceDestination . '/' . $iterator->getSubPathName();
				FileSystem::createDirForPath($path);
				copy($item->getPathName(), $path);
			}
		}
	}


	/**
	 * @param string $name
	 * @return string
	 */
	public function templateExists($name)
	{
		$options = $this->configuration->getOptions();
		return isset($options['template']['templates'][$name]);
	}


	/**
	 * Checks if template exists and creates dir for it.
	 *
	 * @param string $name
	 * @throws \RuntimeException
	 */
	private function prepareTemplate($name)
	{
		if ( ! $this->templateExists($name)) {
			throw new \RuntimeException('Template for ' . $name . ' does not exist or is missing in config');
		}

		$dir = dirname($this->getTemplateFileName($name));
		if ( ! is_dir($dir)) {
			mkdir($dir, 0755, TRUE);
		}
	}


	/**
	 * @return string
	 */
	private function getTemplateDir()
	{
		// todo: this should be in options!
		$config = $this->configuration->getOption('templateConfig');
		$doh = $this->configuration->getOption('template');
		dump($doh['templatePath']);
		die;

		return dirname($config);
	}


	/**
	 * @return string
	 */
	private function getDestination()
	{
		return $this->configuration->getOption(CO::DESTINATION);
	}

}

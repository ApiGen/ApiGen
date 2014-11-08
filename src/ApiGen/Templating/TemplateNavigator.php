<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\TemplateConfiguration;
use ApiGen\FileSystem\FileSystem;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Templating\Filters\SourceFilters;
use Nette;
use Nette\Utils\Finder;
use RecursiveDirectoryIterator;


class TemplateNavigator extends Nette\Object
{

	/**
	 * @var string
	 */
	private $tempDir;

	/**
	 * @var Filters\UrlFilters
	 */
	private $urlFilters;

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var SourceFilters
	 */
	private $sourceFilters;


	public function __construct(
		Filters\UrlFilters $urlFilters,
		Configuration $configuration,
		SourceFilters $sourceFilters
	) {
		$this->urlFilters = $urlFilters;
		$this->configuration = $configuration;
		$this->sourceFilters = $sourceFilters;
	}


	/**
	 * @param string $name
	 * @return string
	 */
	public function getTemplatePath($name)
	{
		$this->prepareTemplate($name);
		$options = $this->configuration->getOptions();
		return $options['template']['templates'][$name]['template']; // note: should be already absolute
	}


	/**
	 * @param string $name
	 * @return string
	 */
	public function getTemplateFileName($name)
	{
		$destination = $this->configuration->getOption('destination');
		$options = $this->configuration->getOptions();
		return $destination . DS . $options['template']['templates'][$name]['filename'];
	}


	/**
	 * @param string $namespace
	 * @return string
	 */
	public function getTemplatePathForNamespace($namespace)
	{
		$destination = $this->configuration->getOption('destination');
		return $destination . DS . $this->urlFilters->namespaceUrl($namespace);
	}


	/**
	 * @param string $package
	 * @return string
	 */
	public function getTemplatePathForPackage($package)
	{
		$destination = $this->configuration->getOption('destination');
		return $destination . DS . $this->urlFilters->packageUrl($package);
	}


	/**
	 * @return string
	 */
	public function getTemplatePathForClass(ReflectionElement $element)
	{
		$destination = $this->configuration->getOption('destination');
		return $destination . DS . $this->urlFilters->classUrl($element);
	}


	/**
	 * @return string
	 */
	public function getTemplatePathForConstant(ReflectionElement $element)
	{
		$destination = $this->configuration->getOption('destination');
		return $destination . DS . $this->urlFilters->constantUrl($element);
	}


	/**
	 * @return string
	 */
	public function getTemplatePathForFunction(ReflectionElement $element)
	{
		$destination = $this->configuration->getOption('destination');
		return $destination . DS . $this->urlFilters->functionUrl($element);
	}


	/**
	 * @return string
	 */
	public function getTemplatePathForSourceElement(ReflectionElement $element)
	{
		$destination = $this->configuration->getOption('destination');
		return $destination . DS . $this->sourceFilters->sourceUrl($element, FALSE);
	}


	public function copyResourcesToDestination()
	{
		$destination = $this->configuration->getOption('destination');
		$templateOptions = $this->configuration->getOptions();
		foreach ($templateOptions['template']['resources'] as $resourceSource => $resourceDestination) {
			// todo: symfony FS

			// File
			$resourcePath = $this->getTemplateDir() . DS . $resourceSource;
			if (is_file($resourcePath)) {
				$path = $destination . DS . $resourceDestination;
				FileSystem::createDirForPath($path);
				copy($resourcePath, $path);
				continue;
			}

			// Dir

			/** @var RecursiveDirectoryIterator $iterator  */
			$iterator = Finder::findFiles('*')->from($resourcePath)->getIterator();
			foreach ($iterator as $item) {
				$path = $destination . DS . $resourceDestination . DS . $iterator->getSubPathName();
				FileSystem::createDirForPath($path);
				copy($item->getPathName(), $path);
			}
		}
	}


	public function prepareTempDir()
	{
		$destination = $this->configuration->getOption('destination');

		// todo: symfony FS
		if ($this->tempDir === NULL) {
			$tempDir = $destination . DS . '_' . uniqid();
			if ( ! is_dir($tempDir)) {
				mkdir($tempDir, 0755, TRUE);
			}
			$this->tempDir = $tempDir;
		}
	}


	public function removeTempDir()
	{
		// todo: symfony FS
		FileSystem::deleteDir($this->tempDir);
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
		return dirname($config);
	}

}

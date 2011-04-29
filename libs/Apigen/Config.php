<?php

/**
 * ApiGen - API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

namespace Apigen;

use Apigen\Exception;
use Nette;

class Config
{
	/**
	 * Options.
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * Parsed configuration.
	 *
	 * @var array
	 */
	private $config = array();

	/**
	 * Default configuration.
	 *
	 * @var array
	 */
	private static $defaultConfig = array(
		'title' => '',
		'baseUrl' => '',
		'googleCse' => '',
		'template' => 'default',
		'templateDir' => '',
		'accessLevels' => array('public', 'protected'),
		'wipeout' => true,
		'progressbar' => true
	);

	/**
	 * On/Off or Yes/No options.
	 *
	 * @var array
	 */
	private static $booleanOptions = array(
		'wipeout',
		'progressbar'
	);

	/**
	 * Options with list of values.
	 *
	 * @var array
	 */
	private static $arrayOptions = array(
		'source' => array(),
		'accessLevels' => array('public', 'protected', 'private')
	);

	/**
	 * File or directory path options.
	 *
	 * @var array
	 */
	private static $pathOptions = array(
		'config',
		'source',
		'destination',
		'templateDir'
	);

	/**
	 * Initializes configuration.
	 *
	 * @param array $options
	 */
	public function __construct(array $options)
	{
		$this->options = $options;

		$this->parse()
			->check();

		// Merge template config
		$this->config = array_merge($this->config, Nette\Utils\Neon::decode(file_get_contents($this->getTemplateConfig())));
	}

	/**
	 * Parses options and configuration.
	 *
	 * @return \Apigen\Config
	 */
	private function parse()
	{
		if (!isset($this->options['config']) && !isset($this->options['source'], $this->options['destination'])) {
			throw new Exception('Missing required options', Exception::INVALID_CONFIG);
		}

		$this->config = self::$defaultConfig;
		$this->config['templateDir'] = realpath(__DIR__ . '/../../templates');

		// Config file
		if (isset($this->options['config'])) {
			if (!is_file($this->options['config'])) {
				throw new Exception(sprintf('Config file %s doesn\'t exist', $this->options['config']), Exception::INVALID_CONFIG);
			}

			$this->config = array_merge($this->config, Nette\Utils\Neon::decode(file_get_contents($this->options['config'])));
		}

		// Parse options
		foreach ($this->options as $option => $value) {
			$option = preg_replace_callback('#-([a-z])#', function($matches) {
				return ucfirst($matches[1]);
			}, $option);

			$this->config[$option] = $value;
		}

		foreach (self::$booleanOptions as $option) {
			$value = strtolower($this->config[$option]);
			if ('on' === $value || 'yes' === $value) {
				$value = true;
			} elseif ('off' === $value || 'no' === $value) {
				$value = false;
			}
			$this->config[$option] = (bool) $value;
		}

		foreach (self::$arrayOptions as $option => $possibleValues) {
			$this->config[$option] = array_unique((array) $this->config[$option]);
			foreach ($this->config[$option] as $key => $value) {
				$value = explode(',', $value);
				while (count($value) > 1) {
					array_push($this->config[$option], array_shift($value));
				}
				$this->config[$option][$key] = array_shift($value);
			}

			if (!empty($possibleValues)) {
				$this->config[$option] = array_filter($this->config[$option], function($value) use ($possibleValues) {
					return in_array($value, $possibleValues);
				});
			}
		}

		foreach (self::$pathOptions as $option) {
			if (is_array($this->config[$option])) {
				array_walk($this->config[$option], function(&$value) {
					if (file_exists($value)) {
						$value = realpath($value);
					}
				});
			} else {
				if (file_exists($this->config[$option])) {
					$this->config[$option] = realpath($this->config[$option]);
				}
			}
		}

		return $this;
	}

	/**
	 * Checks configuration.
	 *
	 * @return \Apigen\Config
	 */
	private function check()
	{
		if (!is_dir($this->config['templateDir'])) {
			throw new Exception(sprintf('Template directory %s doesn\'t exist', $this->config['templateDir']), Exception::INVALID_CONFIG);
		}
		$templateConfig = $this->getTemplateConfig();
		if (!is_dir(dirname($templateConfig))) {
			throw new Exception('Template doesn\'t exist', Exception::INVALID_CONFIG);
		}
		if (!is_file($templateConfig)) {
			throw new Exception('Template config doesn\'t exist', Exception::INVALID_CONFIG);
		}

		if (empty($this->config['source'])) {
			throw new Exception('Source directory is not set', Exception::INVALID_CONFIG);
		}
		foreach ($this->config['source'] as $source) {
			if (!is_dir($source)) {
				throw new Exception(sprintf('Source directory %s doesn\'t exist', $source), Exception::INVALID_CONFIG);
			}
		}
		foreach ($this->config['source'] as $source) {
			foreach ($this->config['source'] as $source2) {
				if ($source !== $source2 && 0 === strpos($source, $source2)) {
					throw new Exception(sprintf('Source directories %s and %s overlap', $source, $source2), Exception::INVALID_CONFIG);
				}
			}
		}

		if (empty($this->config['accessLevels'])) {
			throw new Exception('No supported access level given', Exception::INVALID_CONFIG);
		}

		return $this;
	}

	/**
	 * Returns template config path.
	 *
	 * @return string
	 */
	private function getTemplateConfig()
	{
		return $this->config['templateDir'] . DIRECTORY_SEPARATOR . $this->config['template'] . DIRECTORY_SEPARATOR . 'config.neon';
	}

	public function __isset($name)
	{
		return isset($this->config[$name]);
	}

	public function __get($name)
	{
		return isset($this->config[$name]) ? $this->config[$name] : null;
	}

	public function __set($name, $value)
	{
		$this->config[$name] = $value;
	}

	public function __unset($name)
	{
		unset($this->config[$name]);
	}
}

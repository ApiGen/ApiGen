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

class Config implements \ArrayAccess, \IteratorAggregate
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
		'template' => '',
		'templateDir' => '',
		'accessLevels' => array('public', 'protected'),
		'wipeout' => true,
		'progressbar' => true
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
		$this->config = self::$defaultConfig;

		if (isset($this->options['config'])) {
			// Config file
			if (!is_file($this->options['config'])) {
				throw new Exception(sprintf('Config file %s doesn\'t exist', $this->options['config']), Exception::INVALID_CONFIG);
			}

			$this->config = array_merge($this->config, Nette\Utils\Neon::decode(file_get_contents($this->options['config'])));
		} elseif (isset($this->options['source'], $this->options['destination'])) {
			// Parse options
			foreach ($this->options as $key => $value) {
				$key = preg_replace_callback('#-([a-z])#', function($matches) {
					return ucfirst($matches[1]);
				}, $key);

				$this->config[$key] = $value;
			}

			array_walk_recursive($this->config, function(&$value) {
				$lvalue = strtolower($value);

				if ('on' === $lvalue || 'yes' === $lvalue) {
					$value = true;
				} elseif ('off' === $lvalue || 'no' === $lvalue) {
					$value = false;
				}
			});

		} else {
			throw new Exception('Missing required options', Exception::INVALID_CONFIG);
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
		// Fix
		if (empty($this->config['template'])) {
			$this->config['template'] = 'default';
		}
		if (empty($this->config['templateDir'])) {
			$this->config['templateDir'] = realpath(__DIR__ . '/../../templates');
		}
		$this->config['source'] = array_unique((array) $this->config['source']);
		foreach ($this->config['source'] as $key => $source) {
			if (is_dir($source)) {
				$this->config['source'][$key] = realpath($source);
			}
		}
		foreach (array('destination', 'templateDir') as $key) {
			if (is_dir($this->config[$key])) {
				$this->config[$key] = realpath($this->config[$key]);
			}
		}

		$this->config['accessLevels'] = array_unique((array) $this->config['accessLevels']);
		foreach ($this->config['accessLevels'] as $key => $levels) {
			$levels = explode(',', $levels);
			while (count($levels) > 1) {
				array_push($this->config['accessLevels'], array_shift($levels));
			}
			$this->config['accessLevels'][$key] = array_shift($levels);
		}
		$this->config['accessLevels'] = array_filter($this->config['accessLevels'], function($item) {
			return in_array($item, array('public', 'protected', 'private'));
		});

		// Check
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
		} else {
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

	/**
	 * Returns an iterator over all items.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->config);
	}

	/**
	 * Implements \ArrayAccess::offsetExists.
	 *
	 * @param mixed $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return isset($this->config[$offset]);
	}

	/**
	 * Implements \ArrayAccess::offsetGet.
	 *
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return isset($this->config[$offset]) ? $this->config[$offset] : null;
	}

	/**
	 * Implements \ArrayAccess::offsetSet.
	 *
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		$this->config[$offset] = $value;
	}

	/**
	 * Implements \ArrayAccess::offsetUnset.
	 *
	 * @param mixed $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->config[$offset]);
	}

	public function __isset($name)
	{
		return $this->offsetExists($name);
	}

	public function __get($name)
	{
		return $this->offsetGet($name);
	}

	public function __set($name, $value)
	{
		$this->offsetSet($name, $value);
	}

	public function __unset($name)
	{
		$this->offsetUnset($name);
	}
}

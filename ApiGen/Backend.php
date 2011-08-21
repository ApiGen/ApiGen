<?php

/**
 * ApiGen 2.0.3 - API documentation generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;
use TokenReflection, TokenReflection\IReflectionConstant, TokenReflection\IReflectionFunction, TokenReflection\Broker;

/**
 * Customized TokenReflection broker backend.
 *
 * Adds internal classes from @param, @var, @return, @throws annotations as well
 * as parent classes to the overall class list.
 *
 * @author Ondřej Nešpor
 * @author Jaroslav Hanslík
 */
class Backend extends Broker\Backend\Memory
{
	/**
	 * Generator instance.
	 *
	 * @var \ApiGen\Generator
	 */
	private $generator;

	/**
	 * Cache of processed token streams.
	 *
	 * @var array
	 */
	private $fileCache = array();

	/**
	 * Determines if token streams should be cached in filesystem.
	 *
	 * @var boolean
	 */
	private $cacheTokenStreams = false;

	/**
	 * Constructor.
	 *
	 * @param \ApiGen\Generator $generator Generator instance
	 * @param boolean $cacheTokenStreams If token stream should be cached
	 */
	public function __construct(Generator $generator, $cacheTokenStreams = false)
	{
		$this->generator = $generator;
		$this->cacheTokenStreams = $cacheTokenStreams;
	}

	/**
	 * Destructor.
	 *
	 * Deletes all cached token streams.
	 */
	public function __destruct()
	{
		foreach ($this->fileCache as $file) {
			unlink($file);
		}
	}

	/**
	 * Adds a file to the backend storage.
	 *
	 * @param \TokenReflection\ReflectionFile $file File reflection object
	 * @return \TokenReflection\Broker\Backend\Memory
	 */
	public function addFile(TokenReflection\ReflectionFile $file)
	{
		parent::addFile($file);
		if ($this->cacheTokenStreams) {
			$this->fileCache[$file->getName()] = $cacheFile = tempnam(sys_get_temp_dir(), 'trc');
			file_put_contents($cacheFile, serialize($file->getTokenStream()));
		}
		return $this;
	}

	/**
	 * Returns an array of tokens for a particular file.
	 *
	 * @param string $fileName File name
	 * @return \TokenReflection\Stream
	 * @throws \ApiGen\Exception If the token stream could not be returned
	 */
	public function getFileTokens($fileName)
	{
		try {
			if (!$this->isFileProcessed($fileName)) {
				throw new Exception('File was not processed.');
			}

			$realName = Broker::getRealPath($fileName);
			if (!isset($this->fileCache[$realName])) {
				throw new Exception('File is not in the cache.');
			}

			$data = @file_get_contents($this->fileCache[$realName]);
			if (false === $data) {
				throw new Exception('Cached file is not readable.');
			}
			$file = @unserialize($data);
			if (false === $file) {
				throw new Exception('Stream could not be loaded from cache.');
			}

			return $file;
		} catch (\Exception $e) {
			throw new Exception(sprintf('Could not return token stream for file %s.', $fileName), 0, $e);
		}
	}

	/**
	 * Prepares and returns used class lists.
	 *
	 * @return array
	 */
	protected function parseClassLists()
	{
		$allClasses = array(
			self::TOKENIZED_CLASSES => array(),
			self::INTERNAL_CLASSES => array(),
			self::NONEXISTENT_CLASSES => array()
		);

		$declared = array_flip(array_merge(get_declared_classes(), get_declared_interfaces()));

		foreach ($this->getNamespaces() as $namespace) {
			foreach ($namespace->getClasses() as $name => $trClass) {
				$class = new ReflectionClass($trClass, $this->generator);
				$allClasses[self::TOKENIZED_CLASSES][$name] = $class;
				if (!$class->isDocumented()) {
					continue;
				}

				foreach (array_merge($trClass->getParentClasses(), $trClass->getInterfaces()) as $parentName => $parent) {
					if ($parent->isInternal()) {
						if (!isset($allClasses[self::INTERNAL_CLASSES][$parentName])) {
							$allClasses[self::INTERNAL_CLASSES][$parentName] = $parent;
						}
					} elseif (!$parent->isTokenized()) {
						if (!isset($allClasses[self::NONEXISTENT_CLASSES][$parentName])) {
							$allClasses[self::NONEXISTENT_CLASSES][$parentName] = $parent;
						}
					}
				}

				foreach ($class->getOwnMethods() as $method) {
					$allClasses = $this->processFunction($declared, $allClasses, $method);
				}

				foreach ($class->getOwnProperties() as $property) {
					$annotations = $property->getAnnotations();

					if (!isset($annotations['var'])) {
						continue;
					}

					foreach ($annotations['var'] as $doc) {
						foreach (explode('|', preg_replace('#\s.*#', '', $doc)) as $name) {
							$allClasses = $this->addClass($declared, $allClasses, $name);
						}
					}
				}
			}
		}

		foreach ($this->getFunctions() as $function) {
			$allClasses = $this->processFunction($declared, $allClasses, $function);
		}

		array_walk_recursive($allClasses, function(&$reflection, $name, Generator $generator) {
			if (!$reflection instanceof ReflectionClass) {
				$reflection = new ReflectionClass($reflection, $generator);
			}
		}, $this->generator);

		return $allClasses;
	}

	/**
	 * Processes a function/method and adds classes from annotations to the overall class array.
	 *
	 * @param array $declared Array of declared classes
	 * @param array $allClasses Array with all classes parsed so far
	 * @param \ApiGen\ReflectionFunction|\TokenReflection\IReflectionFunctionBase $function Function/method reflection
	 * @return array
	 */
	private function processFunction(array $declared, array $allClasses, $function)
	{
		static $parsedAnnotations = array('param', 'return', 'throws');

		foreach ($parsedAnnotations as $annotation) {
			$annotations = $function->getAnnotations();

			if (!isset($annotations[$annotation])) {
				continue;
			}

			foreach ($annotations[$annotation] as $doc) {
				foreach (explode('|', preg_replace('#\s.*#', '', $doc)) as $name) {
					$allClasses = $this->addClass($declared, $allClasses, $name);
				}
			}
		}

		foreach ($function->getParameters() as $param) {
			if ($hint = $param->getClass()) {
				$allClasses = $this->addClass($declared, $allClasses, $hint->getName());
			}
		}

		return $allClasses;
	}

	/**
	 * Adds a class to list of classes.
	 *
	 * @param array $declared Array of declared classes
	 * @param array $allClasses Array with all classes parsed so far
	 * @param string $name Class name
	 * @return array
	 */
	private function addClass(array $declared, array $allClasses, $name)
	{
		$name = ltrim($name, '\\');

		if (!isset($declared[$name]) || isset($allClasses[self::TOKENIZED_CLASSES][$name]) || isset($allClasses[self::INTERNAL_CLASSES][$name]) || isset($allClasses[self::NONEXISTENT_CLASSES][$name])) {
			return $allClasses;
		}

		$parameterClass = $this->getBroker()->getClass($name);
		if ($parameterClass->isInternal()) {
			$allClasses[self::INTERNAL_CLASSES][$name] = $parameterClass;
			foreach (array_merge($parameterClass->getInterfaces(), $parameterClass->getParentClasses()) as $parentClass) {
				if (!isset($allClasses[self::INTERNAL_CLASSES][$parentName = $parentClass->getName()])) {
					$allClasses[self::INTERNAL_CLASSES][$parentName] = $parentClass;
				}
			}
		} elseif (!$parameterClass->isTokenized() && !isset($allClasses[self::NONEXISTENT_CLASSES][$name])) {
			$allClasses[self::NONEXISTENT_CLASSES][$name] = $parameterClass;
		}

		return $allClasses;
	}

	/**
	 * Returns all constants from all namespaces.
	 *
	 * @return array
	 */
	public function getConstants()
	{
		$generator = $this->generator;
		return array_map(function(IReflectionConstant $constant) use ($generator) {
			return new ReflectionConstant($constant, $generator);
		}, parent::getConstants());
	}

	/**
	 * Returns all functions from all namespaces.
	 *
	 * @return array
	 */
	public function getFunctions()
	{
		$generator = $this->generator;
		return array_map(function(IReflectionFunction $function) use ($generator) {
			return new ReflectionFunction($function, $generator);
		}, parent::getFunctions());
	}
}

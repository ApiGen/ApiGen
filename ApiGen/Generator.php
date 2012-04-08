<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

use ApiGen\Config\Configuration;
use InvalidArgumentException;
use Nette;
use RuntimeException;
use TokenReflection\Broker;

/**
 * Generates a HTML API documentation.
 */
class Generator extends Object implements IGenerator
{
	/**
	 * Configuration.
	 *
	 * @var \ApiGen\Config
	 */
	private $config;

	/**
	 * Charset convertor.
	 *
	 * @var \ApiGen\CharsetConvertor
	 */
	private $charsetConvertor;

	/**
	 * Source code highlighter.
	 *
	 * @var \ApiGen\ISourceCodeHighlighter
	 */
	private $highlighter;

	/**
	 * List of parsed classes.
	 *
	 * @var \ArrayObject
	 */
	private $parsedClasses = null;

	/**
	 * List of parsed constants.
	 *
	 * @var \ArrayObject
	 */
	private $parsedConstants = null;

	/**
	 * List of parsed functions.
	 *
	 * @var \ArrayObject
	 */
	private $parsedFunctions = null;

	/**
	 * List of packages.
	 *
	 * @var array
	 */
	private $packages = array();

	/**
	 * List of namespaces.
	 *
	 * @var array
	 */
	private $namespaces = array();

	/**
	 * List of classes.
	 *
	 * @var array
	 */
	private $classes = array();

	/**
	 * List of interfaces.
	 *
	 * @var array
	 */
	private $interfaces = array();

	/**
	 * List of traits.
	 *
	 * @var array
	 */
	private $traits = array();

	/**
	 * List of exceptions.
	 *
	 * @var array
	 */
	private $exceptions = array();

	/**
	 * List of constants.
	 *
	 * @var array
	 */
	private $constants = array();

	/**
	 * List of functions.
	 *
	 * @var array
	 */
	private $functions = array();

	/**
	 * List of symlinks.
	 *
	 * @var array
	 */
	private $symlinks = array();

	/**
	 * Sets dependencies.
	 *
	 * @param \ApiGen\Config\Configuration $config
	 * @param \ApiGen\CharsetConvertor $charsetConvertor
	 * @param \ApiGen\ISourceCodeHighlighter $highlighter
	 */
	public function __construct(Configuration $config, CharsetConvertor $charsetConvertor, ISourceCodeHighlighter $highlighter)
	{
		$this->config = $config;
		$this->charsetConvertor = $charsetConvertor;
		$this->highlighter = $highlighter;
		$this->parsedClasses = new \ArrayObject();
		$this->parsedConstants = new \ArrayObject();
		$this->parsedFunctions = new \ArrayObject();
	}

	/**
	 * Scans and parses PHP files.
	 *
	 * @return array
	 * @throws \RuntimeException If no PHP files have been found.
	 */
	public function parse()
	{
		$files = array();

		$flags = \RecursiveDirectoryIterator::CURRENT_AS_FILEINFO | \RecursiveDirectoryIterator::SKIP_DOTS;
		if (defined('\\RecursiveDirectoryIterator::FOLLOW_SYMLINKS')) {
			// Available from PHP 5.3.1
			$flags |= \RecursiveDirectoryIterator::FOLLOW_SYMLINKS;
		}
		foreach ($this->config->source as $source) {
			$entries = array();
			if (is_dir($source)) {
				foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, $flags)) as $entry) {
					if (!$entry->isFile()) {
						continue;
					}
					$entries[] = $entry;
				}
			} elseif ($this->isPhar($source)) {
				if (!extension_loaded('phar')) {
					throw new RuntimeException('Phar extension is not loaded');
				}
				foreach (new \RecursiveIteratorIterator(new \Phar($source, $flags)) as $entry) {
					if (!$entry->isFile()) {
						continue;
					}
					$entries[] = $entry;
				}
			} else {
				$entries[] = new \SplFileInfo($source);
			}

			$regexp = '~\\.' . implode('|', $this->config->extensions->toArray()) . '$~i';
			foreach ($entries as $entry) {
				if (!preg_match($regexp, $entry->getFilename())) {
					continue;
				}
				$pathName = $this->normalizePath($entry->getPathName());
				$unPharName = $this->unPharPath($pathName);
				foreach ($this->config->exclude as $mask) {
					if (fnmatch($mask, $unPharName, FNM_NOESCAPE)) {
						continue 2;
					}
				}

				$files[$pathName] = $entry->getSize();
				if (false !== $entry->getRealPath() && $pathName !== $entry->getRealPath()) {
					$this->symlinks[$entry->getRealPath()] = $pathName;
				}
			}
		}

		if (empty($files)) {
			throw new RuntimeException('No PHP files found');
		}

		$this->fireEvent('parseStart', array_sum($files));

		$broker = new Broker(new Backend($this, !empty($this->config->report)), Broker::OPTION_DEFAULT & ~(Broker::OPTION_PARSE_FUNCTION_BODY | Broker::OPTION_SAVE_TOKEN_STREAM));

		$errors = array();

		foreach ($files as $filePath => $size) {
			$content = $this->charsetConvertor->convertFile($filePath);

			try {
				$broker->processString($content, $filePath);
			} catch (\Exception $e) {
				$errors[] = $e;
			}

			$this->fireEvent('parseProgress', $size);
		}

		// Classes
		$this->parsedClasses->exchangeArray($broker->getClasses(Backend::TOKENIZED_CLASSES | Backend::INTERNAL_CLASSES | Backend::NONEXISTENT_CLASSES));
		$this->parsedClasses->uksort('strcasecmp');

		// Constants
		$this->parsedConstants->exchangeArray($broker->getConstants());
		$this->parsedConstants->uksort('strcasecmp');

		// Functions
		$this->parsedFunctions->exchangeArray($broker->getFunctions());
		$this->parsedFunctions->uksort('strcasecmp');

		$documentedCounter = function($count, $element) {
			return $count += (int) $element->isDocumented();
		};

		return (object) array(
			'classes' => count($broker->getClasses(Backend::TOKENIZED_CLASSES)),
			'constants' => count($this->parsedConstants),
			'functions' => count($this->parsedFunctions),
			'internalClasses' => count($broker->getClasses(Backend::INTERNAL_CLASSES)),
			'documentedClasses' => array_reduce($broker->getClasses(Backend::TOKENIZED_CLASSES), $documentedCounter),
			'documentedConstants' => array_reduce($this->parsedConstants->getArrayCopy(), $documentedCounter),
			'documentedFunctions' => array_reduce($this->parsedFunctions->getArrayCopy(), $documentedCounter),
			'documentedInternalClasses' => array_reduce($broker->getClasses(Backend::INTERNAL_CLASSES), $documentedCounter),
			'errors' => $errors
		);
	}

	/**
	 * Returns configuration.
	 *
	 * @return mixed
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * Returns parsed class list.
	 *
	 * @return \ArrayObject
	 */
	public function getParsedClasses()
	{
		return $this->parsedClasses;
	}

	/**
	 * Returns parsed constant list.
	 *
	 * @return \ArrayObject
	 */
	public function getParsedConstants()
	{
		return $this->parsedConstants;
	}

	/**
	 * Returns parsed function list.
	 *
	 * @return \ArrayObject
	 */
	public function getParsedFunctions()
	{
		return $this->parsedFunctions;
	}

	/**
	 * Wipes out the destination directory.
	 *
	 * @return boolean
	 */
	public function wipeOutDestination()
	{
		foreach ($this->getGeneratedFiles() as $path) {
			if (is_file($path) && !@unlink($path)) {
				return false;
			}
		}

		$archive = $this->getArchivePath();
		if (is_file($archive) && !@unlink($archive)) {
			return false;
		}

		return true;
	}

	/**
	 * Generates API documentation.
	 *
	 * @throws \RuntimeException If destination directory is not writable.
	 */
	public function generate()
	{
		@mkdir($this->config->destination, 0755, true);
		if (!is_dir($this->config->destination) || !is_writable($this->config->destination)) {
			throw new RuntimeException(sprintf('Directory "%s" isn\'t writable', $this->config->destination));
		}

		// Copy resources
		foreach ($this->config->template->resources as $resourceSource => $resourceDestination) {
			// File
			$resourcePath = $this->getTemplateDir() . DIRECTORY_SEPARATOR . $resourceSource;
			if (is_file($resourcePath)) {
				copy($resourcePath, $this->forceDir($this->config->destination . DIRECTORY_SEPARATOR . $resourceDestination));
				continue;
			}

			// Dir
			$iterator = Nette\Utils\Finder::findFiles('*')->from($resourcePath)->getIterator();
			foreach ($iterator as $item) {
				copy($item->getPathName(), $this->forceDir($this->config->destination . DIRECTORY_SEPARATOR . $resourceDestination . DIRECTORY_SEPARATOR . $iterator->getSubPathName()));
			}
		}

		// Categorize by packages and namespaces
		$this->categorize();

		// Prepare progressbar & stuff
		$steps = count($this->packages)
			+ count($this->namespaces)
			+ count($this->classes)
			+ count($this->interfaces)
			+ count($this->traits)
			+ count($this->exceptions)
			+ count($this->constants)
			+ count($this->functions)
			+ count($this->config->template->templates->common)
			+ (int) !empty($this->config->report)
			+ (int) $this->config->tree
			+ (int) $this->config->deprecated
			+ (int) $this->config->todo
			+ (int) $this->config->download
			+ (int) $this->isSitemapEnabled()
			+ (int) $this->isOpensearchEnabled()
			+ (int) $this->isRobotsEnabled();

		if ($this->config->sourceCode) {
			$tokenizedFilter = function(Reflection\ReflectionClass $class) {
				return $class->isTokenized();
			};
			$steps += count(array_filter($this->classes, $tokenizedFilter))
				+ count(array_filter($this->interfaces, $tokenizedFilter))
				+ count(array_filter($this->traits, $tokenizedFilter))
				+ count(array_filter($this->exceptions, $tokenizedFilter))
				+ count($this->constants)
				+ count($this->functions);
			unset($tokenizedFilter);
		}

		$this->fireEvent('generateStart', $steps);

		// Prepare template
		$tmp = $this->config->destination . DIRECTORY_SEPARATOR . 'tmp';
		$this->deleteDir($tmp);
		@mkdir($tmp, 0755, true);
		$template = new Template($this, $this->highlighter);
		$template->setCacheStorage(new Nette\Caching\Storages\PhpFileStorage($tmp));
		$template->generator = Environment::getApplicationName();
		$template->version = Environment::getApplicationVersion();
		$template->config = $this->config;

		// Common files
		$this->generateCommon($template);

		// Optional files
		$this->generateOptional($template);

		// List of poorly documented elements
		if (!empty($this->config->report)) {
			$this->generateReport();
		}

		// List of deprecated elements
		if ($this->config->deprecated) {
			$this->generateDeprecated($template);
		}

		// List of tasks
		if ($this->config->todo) {
			$this->generateTodo($template);
		}

		// Classes/interfaces/traits/exceptions tree
		if ($this->config->tree) {
			$this->generateTree($template);
		}

		// Generate packages summary
		$this->generatePackages($template);

		// Generate namespaces summary
		$this->generateNamespaces($template);

		// Generate classes, interfaces, traits, exceptions, constants and functions files
		$this->generateElements($template);

		// Generate ZIP archive
		if ($this->config->download) {
			$this->generateArchive();
		}

		// Delete temporary directory
		$this->deleteDir($tmp);
	}

	/**
	 * Categorizes by packages and namespaces.
	 *
	 * @return \ApiGen\Generator
	 */
	private function categorize()
	{
		foreach (array('classes', 'constants', 'functions') as $type) {
			foreach ($this->{'parsed' . ucfirst($type)} as $elementName => $element) {
				if (!$element->isDocumented()) {
					continue;
				}

				$packageName = $element->getPseudoPackageName();
				$namespaceName = $element->getPseudoNamespaceName();

				if ($element instanceof Reflection\ReflectionConstant) {
					$this->constants[$elementName] = $element;
					$this->packages[$packageName]['constants'][$elementName] = $element;
					$this->namespaces[$namespaceName]['constants'][$element->getShortName()] = $element;
				} elseif ($element instanceof Reflection\ReflectionFunction) {
					$this->functions[$elementName] = $element;
					$this->packages[$packageName]['functions'][$elementName] = $element;
					$this->namespaces[$namespaceName]['functions'][$element->getShortName()] = $element;
				} elseif ($element->isInterface()) {
					$this->interfaces[$elementName] = $element;
					$this->packages[$packageName]['interfaces'][$elementName] = $element;
					$this->namespaces[$namespaceName]['interfaces'][$element->getShortName()] = $element;
				} elseif ($element->isTrait()) {
					$this->traits[$elementName] = $element;
					$this->packages[$packageName]['traits'][$elementName] = $element;
					$this->namespaces[$namespaceName]['traits'][$element->getShortName()] = $element;
				} elseif ($element->isException()) {
					$this->exceptions[$elementName] = $element;
					$this->packages[$packageName]['exceptions'][$elementName] = $element;
					$this->namespaces[$namespaceName]['exceptions'][$element->getShortName()] = $element;
				} else {
					$this->classes[$elementName] = $element;
					$this->packages[$packageName]['classes'][$elementName] = $element;
					$this->namespaces[$namespaceName]['classes'][$element->getShortName()] = $element;
				}
			}
		}

		// Select only packages or namespaces
		$userPackagesCount = count(array_diff(array_keys($this->packages), array('PHP', 'None')));
		$userNamespacesCount = count(array_diff(array_keys($this->namespaces), array('PHP', 'None')));

		$namespacesEnabled = ('auto' === $this->config->groups && ($userNamespacesCount > 0 || 0 === $userPackagesCount)) || 'namespaces' === $this->config->groups;
		$packagesEnabled = ('auto' === $this->config->groups && !$namespacesEnabled) || 'packages' === $this->config->groups;

		if ($namespacesEnabled) {
			$this->packages = array();
			$this->namespaces = $this->sortGroups($this->namespaces);
		} elseif ($packagesEnabled) {
			$this->namespaces = array();
			$this->packages = $this->sortGroups($this->packages);
		} else {
			$this->namespaces = array();
			$this->packages = array();
		}

		return $this;
	}

	/**
	 * Sorts and filters groups.
	 *
	 * @param array $groups
	 * @return array
	 */
	private function sortGroups(array $groups)
	{
		// Don't generate only 'None' groups
		if (1 === count($groups) && isset($groups['None'])) {
			return array();
		}

		$emptyList = array('classes' => array(), 'interfaces' => array(), 'traits' => array(), 'exceptions' => array(), 'constants' => array(), 'functions' => array());

		$groupNames = array_keys($groups);
		$lowerGroupNames = array_flip(array_map(function($y) {
			return strtolower($y);
		}, $groupNames));

		foreach ($groupNames as $groupName) {
			// Add missing parent groups
			$parent = '';
			foreach (explode('\\', $groupName) as $part) {
				$parent = ltrim($parent . '\\' . $part, '\\');
				if (!isset($lowerGroupNames[strtolower($parent)])) {
					$groups[$parent] = $emptyList;
				}
			}

			// Add missing element types
			foreach ($this->getElementTypes() as $type) {
				if (!isset($groups[$groupName][$type])) {
					$groups[$groupName][$type] = array();
				}
			}
		}

		$main = $this->config->main;
		uksort($groups, function($one, $two) use ($main) {
			// \ as separator has to be first
			$one = str_replace('\\', ' ', $one);
			$two = str_replace('\\', ' ', $two);

			if ($main) {
				if (0 === strpos($one, $main) && 0 !== strpos($two, $main)) {
					return -1;
				} elseif (0 !== strpos($one, $main) && 0 === strpos($two, $main)) {
					return 1;
				}
			}

			return strcasecmp($one, $two);
		});

		return $groups;
	}

	/**
	 * Generates common files.
	 *
	 * @param \ApiGen\Template $template Template
	 * @return \ApiGen\Generator
	 */
	private function generateCommon(Template $template)
	{
		$template->namespace = null;
		$template->namespaces = array_keys($this->namespaces);
		$template->package = null;
		$template->packages = array_keys($this->packages);
		$template->class = null;
		$template->classes = array_filter($this->classes, $this->getMainFilter());
		$template->interfaces = array_filter($this->interfaces, $this->getMainFilter());
		$template->traits = array_filter($this->traits, $this->getMainFilter());
		$template->exceptions = array_filter($this->exceptions, $this->getMainFilter());
		$template->constant = null;
		$template->constants = array_filter($this->constants, $this->getMainFilter());
		$template->function = null;
		$template->functions = array_filter($this->functions, $this->getMainFilter());
		$template->archive = basename($this->getArchivePath());

		// Elements for autocomplete
		$elements = array();
		$autocomplete = array_flip($this->config->autocomplete->toArray());
		foreach ($this->getElementTypes() as $type) {
			foreach ($this->$type as $element) {
				if ($element instanceof Reflection\ReflectionClass) {
					if (isset($autocomplete['classes'])) {
						$elements[] = array('c', $this->getElementName($element));
					}
					if (isset($autocomplete['methods'])) {
						foreach ($element->getOwnMethods() as $method) {
							$elements[] = array('m', $this->getElementName($method));
						}
					}
					if (isset($autocomplete['properties'])) {
						foreach ($element->getOwnProperties() as $property) {
							$elements[] = array('p', $this->getElementName($property));
						}
					}
					if (isset($autocomplete['classconstants'])) {
						foreach ($element->getOwnConstants() as $constant) {
							$elements[] = array('cc', $this->getElementName($constant));
						}
					}
				} elseif ($element instanceof Reflection\ReflectionConstant && isset($autocomplete['constants'])) {
					$elements[] = array('co', $this->getElementName($element));
				} elseif ($element instanceof Reflection\ReflectionFunction && isset($autocomplete['functions'])) {
					$elements[] = array('f', $this->getElementName($element));
				}
			}
		}
		usort($elements, function($one, $two) {
			return strcasecmp($one[1], $two[1]);
		});
		$template->elements = $elements;

		foreach ($this->config->template->templates->common as $source => $destination) {
			$template
				->setFile($this->getTemplateDir() . DIRECTORY_SEPARATOR . $source)
				->save($this->forceDir($this->config->destination . DIRECTORY_SEPARATOR . $destination));

			$this->fireEvent('generateProgress', 1);
		}

		unset($template->elements);

		return $this;
	}

	/**
	 * Generates optional files.
	 *
	 * @param \ApiGen\Template $template Template
	 * @return \ApiGen\Generator
	 */
	private function generateOptional(Template $template)
	{
		if ($this->isSitemapEnabled()) {
			$template
				->setFile($this->getTemplatePath('sitemap', 'optional'))
				->save($this->forceDir($this->getTemplateFileName('sitemap', 'optional')));
			$this->fireEvent('generateProgress', 1);
		}
		if ($this->isOpensearchEnabled()) {
			$template
				->setFile($this->getTemplatePath('opensearch', 'optional'))
				->save($this->forceDir($this->getTemplateFileName('opensearch', 'optional')));
			$this->fireEvent('generateProgress', 1);
		}
		if ($this->isRobotsEnabled()) {
			$template
				->setFile($this->getTemplatePath('robots', 'optional'))
				->save($this->forceDir($this->getTemplateFileName('robots', 'optional')));
			$this->fireEvent('generateProgress', 1);
		}

		return $this;
	}

	/**
	 * Generates list of poorly documented elements.
	 *
	 * @return \ApiGen\Generator
	 * @throws \RuntimeException If file isn't writable.
	 */
	private function generateReport()
	{
		// Function for element labels
		$that = $this;
		$labeler = function($element) use ($that) {
			if ($element instanceof Reflection\ReflectionClass) {
				if ($element->isInterface()) {
					$label = 'interface';
				} elseif ($element->isTrait()) {
					$label = 'trait';
				} elseif ($element->isException()) {
					$label = 'exception';
				} else {
					$label = 'class';
				}
			} elseif ($element instanceof Reflection\ReflectionMethod) {
				$label = 'method';
			} elseif ($element instanceof Reflection\ReflectionFunction) {
				$label = 'function';
			} elseif ($element instanceof Reflection\ReflectionConstant) {
				$label = 'constant';
			} elseif ($element instanceof Reflection\ReflectionProperty) {
				$label = 'property';
			} elseif ($element instanceof Reflection\ReflectionParameter) {
				$label = 'parameter';
			}
			return sprintf('%s %s', $label, $that->getElementName($element));
		};

		$list = array();
		foreach ($this->getElementTypes() as $type) {
			foreach ($this->$type as $parentElement) {
				$fileName = $this->unPharPath($parentElement->getFileName());

				if (!$parentElement->isValid()) {
					$list[$fileName][] = array('error', 0, sprintf('Duplicate %s', $labeler($parentElement)));
					continue;
				}

				// Skip elements not from the main project
				if (!$parentElement->isMain()) {
					continue;
				}

				// Internal elements don't have documentation
				if ($parentElement->isInternal()) {
					continue;
				}

				$elements = array($parentElement);
				if ($parentElement instanceof Reflection\ReflectionClass) {
					$elements = array_merge(
						$elements,
						array_values($parentElement->getOwnMethods()),
						array_values($parentElement->getOwnConstants()),
						array_values($parentElement->getOwnProperties())
					);
				}

				$tokens = $parentElement->getBroker()->getFileTokens($parentElement->getFileName());

				foreach ($elements as $element) {
					$line = $element->getStartLine();
					$label = $labeler($element);

					$annotations = $element->getAnnotations();

					// Documentation
					if (empty($element->shortDescription)) {
						if (empty($annotations)) {
							$list[$fileName][] = array('error', $line, sprintf('Missing documentation of %s', $label));
							continue;
						}
						// Description
						$list[$fileName][] = array('error', $line, sprintf('Missing description of %s', $label));
					}

					// Documentation of method
					if ($element instanceof Reflection\ReflectionMethod || $element instanceof Reflection\ReflectionFunction) {
						// Parameters
						foreach ($element->getParameters() as $no => $parameter) {
							if (!isset($annotations['param'][$no])) {
								$list[$fileName][] = array('error', $line, sprintf('Missing documentation of %s', $labeler($parameter)));
								continue;
							}

							if (!preg_match('~^[\\w\\\\]+(?:\\[\\])?(?:\\|[\\w\\\\]+(?:\\[\\])?)*(?:\\s+\\$' . $parameter->getName() . ')?(?:\\s+.+)?$~s', $annotations['param'][$no])) {
								$list[$fileName][] = array('warning', $line, sprintf('Invalid documentation "%s" of %s', $annotations['param'][$no], $labeler($parameter)));
							}

							unset($annotations['param'][$no]);
						}
						if (isset($annotations['param'])) {
							foreach ($annotations['param'] as $annotation) {
								$list[$fileName][] = array('warning', $line, sprintf('Existing documentation "%s" of nonexistent parameter of %s', $annotation, $label));
							}
						}

						// Return values
						$return = false;
						$tokens->seek($element->getStartPosition())
							->find(T_FUNCTION);
						while ($tokens->next() && $tokens->key() < $element->getEndPosition()) {
							$type = $tokens->getType();
							if (T_FUNCTION === $type) {
								// Skip annonymous functions
								$tokens->find('{')->findMatchingBracket();
							} elseif (T_RETURN === $type && !$tokens->skipWhitespaces()->is(';')) {
								// Skip return without return value
								$return = true;
								break;
							}
						}
						if ($return && !isset($annotations['return'])) {
							$list[$fileName][] = array('error', $line, sprintf('Missing documentation of return value of %s', $label));
						} elseif (isset($annotations['return'])) {
							if (!$return && 'void' !== $annotations['return'][0] && ($element instanceof Reflection\ReflectionFunction || (!$parentElement->isInterface() && !$element->isAbstract()))) {
								$list[$fileName][] = array('warning', $line, sprintf('Existing documentation "%s" of nonexistent return value of %s', $annotations['return'][0], $label));
							} elseif (!preg_match('~^[\\w\\\\]+(?:\\[\\])?(?:\\|[\\w\\\\]+(?:\\[\\])?)*(?:\\s+.+)?$~s', $annotations['return'][0])) {
								$list[$fileName][] = array('warning', $line, sprintf('Invalid documentation "%s" of return value of %s', $annotations['return'][0], $label));
							}
						}
						if (isset($annotations['return'][1])) {
							$list[$fileName][] = array('warning', $line, sprintf('Duplicate documentation "%s" of return value of %s', $annotations['return'][1], $label));
						}

						// Throwing exceptions
						$throw = false;
						$tokens->seek($element->getStartPosition())
							->find(T_FUNCTION);
						while ($tokens->next() && $tokens->key() < $element->getEndPosition()) {
							$type = $tokens->getType();
							if (T_TRY === $type) {
								// Skip try
								$tokens->find('{')->findMatchingBracket();
							} elseif (T_THROW === $type) {
								$throw = true;
								break;
							}
						}
						if ($throw && !isset($annotations['throws'])) {
							$list[$fileName][] = array('error', $line, sprintf('Missing documentation of throwing an exception of %s', $label));
						} elseif (isset($annotations['throws'])	&& !preg_match('~^[\\w\\\\]+(?:\\|[\\w\\\\]+)*(?:\\s+.+)?$~s', $annotations['throws'][0])) {
							$list[$fileName][] = array('warning', $line, sprintf('Invalid documentation "%s" of throwing an exception of %s', $annotations['throws'][0], $label));
						}
					}

					// Data type of constants & properties
					if ($element instanceof Reflection\ReflectionProperty || $element instanceof Reflection\ReflectionConstant) {
						if (!isset($annotations['var'])) {
							$list[$fileName][] = array('error', $line, sprintf('Missing documentation of the data type of %s', $label));
						} elseif (!preg_match('~^[\\w\\\\]+(?:\\[\\])?(?:\\|[\\w\\\\]+(?:\\[\\])?)*(?:\\s+.+)?$~s', $annotations['var'][0])) {
							$list[$fileName][] = array('warning', $line, sprintf('Invalid documentation "%s" of the data type of %s', $annotations['var'][0], $label));
						}

						if (isset($annotations['var'][1])) {
							$list[$fileName][] = array('warning', $line, sprintf('Duplicate documentation "%s" of the data type of %s', $annotations['var'][1], $label));
						}
					}
				}
				unset($tokens);
			}
		}
		uksort($list, 'strcasecmp');

		$file = @fopen($this->config->report, 'w');
		if (false === $file) {
			throw new RuntimeException(sprintf('File "%s" isn\'t writable', $this->config->report));
		}
		fwrite($file, sprintf('<?xml version="1.0" encoding="UTF-8"?>%s', "\n"));
		fwrite($file, sprintf('<checkstyle version="1.3.0">%s', "\n"));
		foreach ($list as $fileName => $reports) {
			fwrite($file, sprintf('%s<file name="%s">%s', "\t", $fileName, "\n"));

			// Sort by line
			usort($reports, function($one, $two) {
				return strnatcmp($one[1], $two[1]);
			});

			foreach ($reports as $report) {
				list($severity, $line, $message) = $report;
				$message = preg_replace('~\\s+~u', ' ', $message);
				fwrite($file, sprintf('%s<error severity="%s" line="%s" message="%s" source="ApiGen.Documentation.Documentation"/>%s', "\t\t", $severity, $line, htmlspecialchars($message), "\n"));
			}

			fwrite($file, sprintf('%s</file>%s', "\t", "\n"));
		}
		fwrite($file, sprintf('</checkstyle>%s', "\n"));
		fclose($file);

		$this->fireEvent('generateProgress', 1);

		return $this;
	}

	/**
	 * Generates list of deprecated elements.
	 *
	 * @param \ApiGen\Template $template Template
	 * @return \ApiGen\Generator
	 * @throws \RuntimeException If template is not set.
	 */
	private function generateDeprecated(Template $template)
	{
		$this->prepareTemplate('deprecated');

		$deprecatedFilter = function($element) {
			return $element->isDeprecated();
		};

		$template->deprecatedMethods = array();
		$template->deprecatedConstants = array();
		$template->deprecatedProperties = array();
		foreach (array_reverse($this->getElementTypes()) as $type) {
			$template->{'deprecated' . ucfirst($type)} = array_filter(array_filter($this->$type, $this->getMainFilter()), $deprecatedFilter);

			if ('constants' === $type || 'functions' === $type) {
				continue;
			}

			foreach ($this->$type as $class) {
				if (!$class->isMain()) {
					continue;
				}

				if ($class->isDeprecated()) {
					continue;
				}

				$template->deprecatedMethods = array_merge($template->deprecatedMethods, array_values(array_filter($class->getOwnMethods(), $deprecatedFilter)));
				$template->deprecatedConstants = array_merge($template->deprecatedConstants, array_values(array_filter($class->getOwnConstants(), $deprecatedFilter)));
				$template->deprecatedProperties = array_merge($template->deprecatedProperties, array_values(array_filter($class->getOwnProperties(), $deprecatedFilter)));
			}
		}
		usort($template->deprecatedMethods, array($this, 'sortMethods'));
		usort($template->deprecatedConstants, array($this, 'sortConstants'));
		usort($template->deprecatedFunctions, array($this, 'sortFunctions'));
		usort($template->deprecatedProperties, array($this, 'sortProperties'));

		$template
			->setFile($this->getTemplatePath('deprecated'))
			->save($this->forceDir($this->getTemplateFileName('deprecated')));

		foreach ($this->getElementTypes() as $type) {
			unset($template->{'deprecated' . ucfirst($type)});
		}
		unset($template->deprecatedMethods);
		unset($template->deprecatedProperties);

		$this->fireEvent('generateProgress', 1);

		return $this;
	}

	/**
	 * Generates list of tasks.
	 *
	 * @param \ApiGen\Template $template Template
	 * @return \ApiGen\Generator
	 * @throws \RuntimeException If template is not set.
	 */
	private function generateTodo(Template $template)
	{
		$this->prepareTemplate('todo');

		$todoFilter = function($element) {
			return $element->hasAnnotation('todo');
		};

		$template->todoMethods = array();
		$template->todoConstants = array();
		$template->todoProperties = array();
		foreach (array_reverse($this->getElementTypes()) as $type) {
			$template->{'todo' . ucfirst($type)} = array_filter(array_filter($this->$type, $this->getMainFilter()), $todoFilter);

			if ('constants' === $type || 'functions' === $type) {
				continue;
			}

			foreach ($this->$type as $class) {
				if (!$class->isMain()) {
					continue;
				}

				$template->todoMethods = array_merge($template->todoMethods, array_values(array_filter($class->getOwnMethods(), $todoFilter)));
				$template->todoConstants = array_merge($template->todoConstants, array_values(array_filter($class->getOwnConstants(), $todoFilter)));
				$template->todoProperties = array_merge($template->todoProperties, array_values(array_filter($class->getOwnProperties(), $todoFilter)));
			}
		}
		usort($template->todoMethods, array($this, 'sortMethods'));
		usort($template->todoConstants, array($this, 'sortConstants'));
		usort($template->todoFunctions, array($this, 'sortFunctions'));
		usort($template->todoProperties, array($this, 'sortProperties'));

		$template
			->setFile($this->getTemplatePath('todo'))
			->save($this->forceDir($this->getTemplateFileName('todo')));

		foreach ($this->getElementTypes() as $type) {
			unset($template->{'todo' . ucfirst($type)});
		}
		unset($template->todoMethods);
		unset($template->todoProperties);

		$this->fireEvent('generateProgress', 1);

		return $this;
	}

	/**
	 * Generates classes/interfaces/traits/exceptions tree.
	 *
	 * @param \ApiGen\Template $template Template
	 * @return \ApiGen\Generator
	 * @throws \RuntimeException If template is not set.
	 */
	private function generateTree(Template $template)
	{
		$this->prepareTemplate('tree');

		$classTree = array();
		$interfaceTree = array();
		$traitTree = array();
		$exceptionTree = array();

		$processed = array();
		foreach ($this->parsedClasses as $className => $reflection) {
			if (!$reflection->isMain() || !$reflection->isDocumented() || isset($processed[$className])) {
				continue;
			}

			if (null === $reflection->getParentClassName()) {
				// No parent classes
				if ($reflection->isInterface()) {
					$t = &$interfaceTree;
				} elseif ($reflection->isTrait()) {
					$t = &$traitTree;
				} elseif ($reflection->isException()) {
					$t = &$exceptionTree;
				} else {
					$t = &$classTree;
				}
			} else {
				foreach (array_values(array_reverse($reflection->getParentClasses())) as $level => $parent) {
					if (0 === $level) {
						// The topmost parent decides about the reflection type
						if ($parent->isInterface()) {
							$t = &$interfaceTree;
						} elseif ($parent->isTrait()) {
							$t = &$traitTree;
						} elseif ($parent->isException()) {
							$t = &$exceptionTree;
						} else {
							$t = &$classTree;
						}
					}
					$parentName = $parent->getName();

					if (!isset($t[$parentName])) {
						$t[$parentName] = array();
						$processed[$parentName] = true;
						ksort($t, SORT_STRING);
					}

					$t = &$t[$parentName];
				}
			}
			$t[$className] = array();
			ksort($t, SORT_STRING);
			$processed[$className] = true;
			unset($t);
		}

		$template->classTree = new Tree($classTree, $this->parsedClasses);
		$template->interfaceTree = new Tree($interfaceTree, $this->parsedClasses);
		$template->traitTree = new Tree($traitTree, $this->parsedClasses);
		$template->exceptionTree = new Tree($exceptionTree, $this->parsedClasses);

		$template
			->setFile($this->getTemplatePath('tree'))
			->save($this->forceDir($this->getTemplateFileName('tree')));

		unset($template->classTree);
		unset($template->interfaceTree);
		unset($template->traitTree);
		unset($template->exceptionTree);

		$this->fireEvent('generateProgress', 1);

		return $this;
	}

	/**
	 * Generates packages summary.
	 *
	 * @param \ApiGen\Template $template Template
	 * @return \ApiGen\Generator
	 * @throws \RuntimeException If template is not set.
	 */
	private function generatePackages(Template $template)
	{
		if (empty($this->packages)) {
			return $this;
		}

		$this->prepareTemplate('package');

		$template->namespace = null;

		foreach ($this->packages as $packageName => $package) {
			$template->package = $packageName;
			$template->subpackages = array_filter($template->packages, function($subpackageName) use ($packageName) {
				return (bool) preg_match('~^' . preg_quote($packageName) . '\\\\[^\\\\]+$~', $subpackageName);
			});
			$template->classes = $package['classes'];
			$template->interfaces = $package['interfaces'];
			$template->traits = $package['traits'];
			$template->exceptions = $package['exceptions'];
			$template->constants = $package['constants'];
			$template->functions = $package['functions'];
			$template
				->setFile($this->getTemplatePath('package'))
				->save($this->config->destination . DIRECTORY_SEPARATOR . $template->getPackageUrl($packageName));

			$this->fireEvent('generateProgress', 1);
		}
		unset($template->subpackages);

		return $this;
	}

	/**
	 * Generates namespaces summary.
	 *
	 * @param \ApiGen\Template $template Template
	 * @return \ApiGen\Generator
	 * @throws \RuntimeException If template is not set.
	 */
	private function generateNamespaces(Template $template)
	{
		if (empty($this->namespaces)) {
			return $this;
		}

		$this->prepareTemplate('namespace');

		$template->package = null;

		foreach ($this->namespaces as $namespaceName => $namespace) {
			$template->namespace = $namespaceName;
			$template->subnamespaces = array_filter($template->namespaces, function($subnamespaceName) use ($namespaceName) {
				return (bool) preg_match('~^' . preg_quote($namespaceName) . '\\\\[^\\\\]+$~', $subnamespaceName);
			});
			$template->classes = $namespace['classes'];
			$template->interfaces = $namespace['interfaces'];
			$template->traits = $namespace['traits'];
			$template->exceptions = $namespace['exceptions'];
			$template->constants = $namespace['constants'];
			$template->functions = $namespace['functions'];
			$template
				->setFile($this->getTemplatePath('namespace'))
				->save($this->config->destination . DIRECTORY_SEPARATOR . $template->getNamespaceUrl($namespaceName));

			$this->fireEvent('generateProgress', 1);
		}
		unset($template->subnamespaces);

		return $this;
	}

	/**
	 * Generate classes, interfaces, traits, exceptions, constants and functions files.
	 *
	 * @param Template $template Template
	 * @return \ApiGen\Generator
	 * @throws \RuntimeException If template is not set.
	 */
	private function generateElements(Template $template)
	{
		if (!empty($this->classes) || !empty($this->interfaces) || !empty($this->traits) || !empty($this->exceptions)) {
			$this->prepareTemplate('class');
		}
		if (!empty($this->constants)) {
			$this->prepareTemplate('constant');
		}
		if (!empty($this->functions)) {
			$this->prepareTemplate('function');
		}
		if ($this->config->sourceCode) {
			$this->prepareTemplate('source');
		}

		// Add @usedby annotation
		foreach ($this->getElementTypes() as $type) {
			foreach ($this->$type as $parentElement) {
				$elements = array($parentElement);
				if ($parentElement instanceof Reflection\ReflectionClass) {
					$elements = array_merge(
						$elements,
						array_values($parentElement->getOwnMethods()),
						array_values($parentElement->getOwnConstants()),
						array_values($parentElement->getOwnProperties())
					);
				}
				foreach ($elements as $element) {
					$uses = $element->getAnnotation('uses');
					if (null === $uses) {
						continue;
					}
					foreach ($uses as $value) {
						list($link, $description) = preg_split('~\s+|$~', $value, 2);
						$resolved = $this->resolveElement($link, $element);
						if (null !== $resolved) {
							$resolved->addAnnotation('usedby', $this->getElementName($element) . ' ' . $description);
						}
					}
				}
			}
		}

		$template->package = null;
		$template->namespace = null;
		$template->classes = $this->classes;
		$template->interfaces = $this->interfaces;
		$template->traits = $this->traits;
		$template->exceptions = $this->exceptions;
		$template->constants = $this->constants;
		$template->functions = $this->functions;
		foreach ($this->getElementTypes() as $type) {
			foreach ($this->$type as $element) {
				if (!empty($this->namespaces)) {
					$template->namespace = $namespaceName = $element->getPseudoNamespaceName();
					$template->classes = $this->namespaces[$namespaceName]['classes'];
					$template->interfaces = $this->namespaces[$namespaceName]['interfaces'];
					$template->traits = $this->namespaces[$namespaceName]['traits'];
					$template->exceptions = $this->namespaces[$namespaceName]['exceptions'];
					$template->constants = $this->namespaces[$namespaceName]['constants'];
					$template->functions = $this->namespaces[$namespaceName]['functions'];
				} elseif (!empty($this->packages)) {
					$template->package = $packageName = $element->getPseudoPackageName();
					$template->classes = $this->packages[$packageName]['classes'];
					$template->interfaces = $this->packages[$packageName]['interfaces'];
					$template->traits = $this->packages[$packageName]['traits'];
					$template->exceptions = $this->packages[$packageName]['exceptions'];
					$template->constants = $this->packages[$packageName]['constants'];
					$template->functions = $this->packages[$packageName]['functions'];
				}

				$template->class = null;
				$template->constant = null;
				$template->function = null;
				if ($element instanceof Reflection\ReflectionClass) {
					// Class
					$template->tree = array_merge(array_reverse($element->getParentClasses()), array($element));

					$template->directSubClasses = $element->getDirectSubClasses();
					uksort($template->directSubClasses, 'strcasecmp');
					$template->indirectSubClasses = $element->getIndirectSubClasses();
					uksort($template->indirectSubClasses, 'strcasecmp');

					$template->directImplementers = $element->getDirectImplementers();
					uksort($template->directImplementers, 'strcasecmp');
					$template->indirectImplementers = $element->getIndirectImplementers();
					uksort($template->indirectImplementers, 'strcasecmp');

					$template->directUsers = $element->getDirectUsers();
					uksort($template->directUsers, 'strcasecmp');
					$template->indirectUsers = $element->getIndirectUsers();
					uksort($template->indirectUsers, 'strcasecmp');

					$template->ownMethods = $element->getOwnMethods();
					$template->ownConstants = $element->getOwnConstants();
					$template->ownProperties = $element->getOwnProperties();

					$template->class = $element;

					$template
						->setFile($this->getTemplatePath('class'))
						->save($this->config->destination . DIRECTORY_SEPARATOR . $template->getClassUrl($element));
				} elseif ($element instanceof Reflection\ReflectionConstant) {
					// Constant
					$template->constant = $element;

					$template
						->setFile($this->getTemplatePath('constant'))
						->save($this->config->destination . DIRECTORY_SEPARATOR . $template->getConstantUrl($element));
				} elseif ($element instanceof Reflection\ReflectionFunction) {
					// Function
					$template->function = $element;

					$template
						->setFile($this->getTemplatePath('function'))
						->save($this->config->destination . DIRECTORY_SEPARATOR . $template->getFunctionUrl($element));
				}

				$this->fireEvent('generateProgress', 1);

				// Generate source codes
				if ($this->config->sourceCode && $element->isTokenized()) {
					$template->fileName = $this->getRelativePath($element->getFileName());
					$template->source = $this->highlighter->highlight($this->charsetConvertor->convertFile($element->getFileName()), true);
					$template
						->setFile($this->getTemplatePath('source'))
						->save($this->config->destination . DIRECTORY_SEPARATOR . $template->getSourceUrl($element, false));

					$this->fireEvent('generateProgress', 1);
				}
			}
		}

		return $this;
	}

	/**
	 * Creates ZIP archive.
	 *
	 * @return \ApiGen\Generator
	 * @throws \RuntimeException If something went wrong.
	 */
	private function generateArchive()
	{
		if (!extension_loaded('zip')) {
			throw new RuntimeException('Extension zip is not loaded');
		}

		$archive = new \ZipArchive();
		if (true !== $archive->open($this->getArchivePath(), \ZipArchive::CREATE)) {
			throw new RuntimeException('Could not open ZIP archive');
		}

		$archive->setArchiveComment(trim(sprintf('%s API documentation generated by %s %s on %s', $this->config->title, Environment::getApplicationName(), Environment::getApplicationVersion(), date('Y-m-d H:i:s'))));

		$directory = Nette\Utils\Strings::webalize(trim(sprintf('%s API documentation', $this->config->title)), null, false);
		$destinationLength = strlen($this->config->destination);
		foreach ($this->getGeneratedFiles() as $file) {
			if (is_file($file)) {
				$archive->addFile($file, $directory . DIRECTORY_SEPARATOR . substr($file, $destinationLength + 1));
			}
		}

		if (false === $archive->close()) {
			throw new RuntimeException('Could not save ZIP archive');
		}

		$this->fireEvent('generateProgress', 1);

		return $this;
	}

	/**
	 * Tries to resolve string as class, interface or exception name.
	 *
	 * @param string $className Class name description
	 * @param string $namespace Namespace name
	 * @return \ApiGen\Reflection\ReflectionClass
	 */
	public function getClass($className, $namespace = '')
	{
		if (isset($this->parsedClasses[$namespace . '\\' . $className])) {
			$class = $this->parsedClasses[$namespace . '\\' . $className];
		} elseif (isset($this->parsedClasses[$className])) {
			$class = $this->parsedClasses[$className];
		} else {
			return null;
		}

		// Class is not "documented"
		if (!$class->isDocumented()) {
			return null;
		}

		return $class;
	}

	/**
	 * Tries to resolve type as constant name.
	 *
	 * @param string $constantName Constant name
	 * @param string $namespace Namespace name
	 * @return \ApiGen\Reflection\ReflectionConstant
	 */
	public function getConstant($constantName, $namespace = '')
	{
		if (isset($this->parsedConstants[$namespace . '\\' . $constantName])) {
			$constant = $this->parsedConstants[$namespace . '\\' . $constantName];
		} elseif (isset($this->parsedConstants[$constantName])) {
			$constant = $this->parsedConstants[$constantName];
		} else {
			return null;
		}

		// Constant is not "documented"
		if (!$constant->isDocumented()) {
			return null;
		}

		return $constant;
	}

	/**
	 * Tries to resolve type as function name.
	 *
	 * @param string $functionName Function name
	 * @param string $namespace Namespace name
	 * @return \ApiGen\Reflection\ReflectionFunction
	 */
	public function getFunction($functionName, $namespace = '')
	{
		if (isset($this->parsedFunctions[$namespace . '\\' . $functionName])) {
			$function = $this->parsedFunctions[$namespace . '\\' . $functionName];
		} elseif (isset($this->parsedFunctions[$functionName])) {
			$function = $this->parsedFunctions[$functionName];
		} else {
			return null;
		}

		// Function is not "documented"
		if (!$function->isDocumented()) {
			return null;
		}

		return $function;
	}

	/**
	 * Tries to parse a definition of a class/method/property/constant/function and returns the appropriate instance if successful.
	 *
	 * @param string $definition Definition
	 * @param \ApiGen\Reflection\ReflectionElement $context Link context
	 * @param string $expectedName Expected element name
	 * @return \ApiGen\Reflection\ReflectionElement|null
	 */
	public function resolveElement($definition, Reflection\ReflectionElement $context, &$expectedName = null)
	{
		// No simple type resolving
		static $types = array(
			'boolean' => 1, 'integer' => 1, 'float' => 1, 'string' => 1,
			'array' => 1, 'object' => 1, 'resource' => 1, 'callback' => 1,
			'callable' => 1, 'null' => 1, 'false' => 1, 'true' => 1, 'mixed' => 1
		);

		if (empty($definition) || isset($types[$definition])) {
			return null;
		}

		if ($context instanceof Reflection\ReflectionParameter && null === $context->getDeclaringClassName()) {
			// Parameter of function in namespace or global space
			$context = $this->getFunction($context->getDeclaringFunctionName());
		} elseif ($context instanceof Reflection\ReflectionMethod || $context instanceof Reflection\ReflectionParameter
			|| ($context instanceof Reflection\ReflectionConstant && null !== $context->getDeclaringClassName())
			|| $context instanceof Reflection\ReflectionProperty
		) {
			// Member of a class
			$context = $this->getClass($context->getDeclaringClassName());
		}

		$namespaceAliases = $context->getNamespaceAliases();
		if (isset($namespaceAliases[$definition]) && $definition !== ($className = \TokenReflection\Resolver::resolveClassFQN($definition, $namespaceAliases, $context->getNamespaceName()))) {
			// Aliased class
			$expectedName = $className;
			return $this->getClass($className, $context->getNamespaceName());
		} elseif ($class = $this->getClass($definition, $context->getNamespaceName())) {
			// Class
			return $class;
		} elseif ($constant = $this->getConstant($definition, $context->getNamespaceName())) {
			// Constant
			return $constant;
		} elseif (($function = $this->getFunction($definition, $context->getNamespaceName()))
			|| ('()' === substr($definition, -2) && ($function = $this->getFunction(substr($definition, 0, -2), $context->getNamespaceName())))
		) {
			// Function
			return $function;
		} elseif (($pos = strpos($definition, '::')) || ($pos = strpos($definition, '->'))) {
			// Class::something or Class->something
			if (0 === strpos($definition, 'parent::') && ($parentClassName = $context->getParentClassName())) {
				$context = $this->getClass($parentClassName);
			} elseif (0 !== strpos($definition, 'self::')) {
				$class = $this->getClass(substr($definition, 0, $pos), $context->getNamespaceName());

				if (null === $class) {
					$class = $this->getClass(\TokenReflection\Resolver::resolveClassFQN(substr($definition, 0, $pos), $context->getNamespaceAliases(), $context->getNamespaceName()));
				}

				$context = $class;
			}

			$definition = substr($definition, $pos + 2);
		}

		// No usable context
		if (null === $context || $context instanceof Reflection\ReflectionConstant || $context instanceof Reflection\ReflectionFunction) {
			return null;
		}

		if ($context->hasProperty($definition)) {
			// Class property
			return $context->getProperty($definition);
		} elseif ('$' === $definition{0} && $context->hasProperty(substr($definition, 1))) {
			// Class $property
			return $context->getProperty(substr($definition, 1));
		} elseif ($context->hasMethod($definition)) {
			// Class method
			return $context->getMethod($definition);
		} elseif ('()' === substr($definition, -2) && $context->hasMethod(substr($definition, 0, -2))) {
			// Class method()
			return $context->getMethod(substr($definition, 0, -2));
		} elseif ($context->hasConstant($definition)) {
			// Class constant
			return $context->getConstant($definition);
		}

		return null;
	}

	/**
	 * Removes phar:// from the path.
	 *
	 * @param string $path Path
	 * @return string
	 */
	public function unPharPath($path)
	{
		if (0 === strpos($path, 'phar://')) {
			$path = substr($path, 7);
		}
		return $path;
	}

	/**
	 * Adds phar:// to the path.
	 *
	 * @param string $path Path
	 * @return string
	 */
	private function pharPath($path)
	{
		return 'phar://' . $path;
	}

	/**
	 * Checks if given path is a phar.
	 *
	 * @param string $path
	 * @return boolean
	 */
	private function isPhar($path)
	{
		return (bool) preg_match('~\\.phar(?:\\.zip|\\.tar|(?:(?:\\.tar)?(?:\\.gz|\\.bz2))|$)~i', $path);
	}

	/**
	 * Normalizes directory separators in given path.
	 *
	 * @param string $path Path
	 * @return string
	 */
	private function normalizePath($path)
	{
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		$path = str_replace('phar:\\\\', 'phar://', $path);
		return $path;
	}

	/**
	 * Checks if sitemap.xml is enabled.
	 *
	 * @return boolean
	 */
	private function isSitemapEnabled()
	{
		return !empty($this->config->baseUrl) && $this->templateExists('sitemap', 'optional');
	}

	/**
	 * Checks if opensearch.xml is enabled.
	 *
	 * @return boolean
	 */
	private function isOpensearchEnabled()
	{
		return !empty($this->config->googleCseId) && !empty($this->config->baseUrl) && $this->templateExists('opensearch', 'optional');
	}

	/**
	 * Checks if robots.txt is enabled.
	 *
	 * @return boolean
	 */
	private function isRobotsEnabled()
	{
		return !empty($this->config->baseUrl) && $this->templateExists('robots', 'optional');
	}

	/**
	 * Sorts methods by FQN.
	 *
	 * @param \ApiGen\Reflection\ReflectionMethod $one
	 * @param \ApiGen\Reflection\ReflectionMethod $two
	 * @return integer
	 */
	private function sortMethods(Reflection\ReflectionMethod $one, Reflection\ReflectionMethod $two)
	{
		return strcasecmp($one->getDeclaringClassName() . '::' . $one->getName(), $two->getDeclaringClassName() . '::' . $two->getName());
	}

	/**
	 * Sorts constants by FQN.
	 *
	 * @param \ApiGen\Reflection\ReflectionConstant $one
	 * @param \ApiGen\Reflection\ReflectionConstant $two
	 * @return integer
	 */
	private function sortConstants(Reflection\ReflectionConstant $one, Reflection\ReflectionConstant $two)
	{
		return strcasecmp(($one->getDeclaringClassName() ?: $one->getNamespaceName()) . '\\' .  $one->getName(), ($two->getDeclaringClassName() ?: $two->getNamespaceName()) . '\\' .  $two->getName());
	}

	/**
	 * Sorts functions by FQN.
	 *
	 * @param \ApiGen\Reflection\ReflectionFunction $one
	 * @param \ApiGen\Reflection\ReflectionFunction $two
	 * @return integer
	 */
	private function sortFunctions(Reflection\ReflectionFunction $one, Reflection\ReflectionFunction $two)
	{
		return strcasecmp($one->getNamespaceName() . '\\' . $one->getName(), $two->getNamespaceName() . '\\' . $two->getName());
	}

	/**
	 * Sorts functions by FQN.
	 *
	 * @param \ApiGen\Reflection\ReflectionProperty $one
	 * @param \ApiGen\Reflection\ReflectionProperty $two
	 * @return integer
	 */
	private function sortProperties(Reflection\ReflectionProperty $one, Reflection\ReflectionProperty $two)
	{
		return strcasecmp($one->getDeclaringClassName() . '::' . $one->getName(), $two->getDeclaringClassName() . '::' . $two->getName());
	}

	/**
	 * Returns list of element types.
	 *
	 * @return array
	 */
	private function getElementTypes()
	{
		static $types = array('classes', 'interfaces', 'traits', 'exceptions', 'constants', 'functions');
		return $types;
	}

	/**
	 * Returns element name.
	 *
	 * @param \Apigen\Reflection\ReflectionElement $element
	 * @return string
	 */
	public function getElementName(Reflection\ReflectionElement $element)
	{
		if ($element instanceof Reflection\ReflectionClass) {
			return $element->getName();
		} elseif ($element instanceof Reflection\ReflectionMethod) {
			return sprintf('%s::%s()', $element->getDeclaringClassName(), $element->getName());
		} elseif ($element instanceof Reflection\ReflectionFunction) {
			return sprintf('%s()', $element->getName());
		} elseif ($element instanceof Reflection\ReflectionConstant) {
			if ($className = $element->getDeclaringClassName()) {
				return sprintf('%s::%s', $className, $element->getName());
			} else {
				return sprintf('%s', $element->getName());
			}
		} elseif ($element instanceof Reflection\ReflectionProperty) {
			return sprintf('%s::$%s', $element->getDeclaringClassName(), $element->getName());
		} elseif ($element instanceof Reflection\ReflectionParameter) {
			return sprintf('%s($%s)', $this->getElementName($element->getDeclaringFunction()), $element->getName());
		}
	}

	/**
	 * Returns main filter.
	 *
	 * @return \Closure
	 */
	private function getMainFilter()
	{
		return function($element) {
			return $element->isMain();
		};
	}

	/**
	 * Returns ZIP archive path.
	 *
	 * @return string
	 */
	private function getArchivePath()
	{
		$name = trim(sprintf('%s API documentation', $this->config->title));
		return $this->config->destination . DIRECTORY_SEPARATOR . Nette\Utils\Strings::webalize($name) . '.zip';
	}

	/**
	 * Returns filename relative path to the source directory.
	 *
	 * @param string $fileName
	 * @return string
	 * @throws \InvalidArgumentException If relative path could not be determined.
	 */
	public function getRelativePath($fileName)
	{
		if (isset($this->symlinks[$fileName])) {
			$fileName = $this->symlinks[$fileName];
		}
		foreach ($this->config->source as $source) {
			if ($this->isPhar($source)) {
				$source = $this->pharPath($source);
			}
			if (0 === strpos($fileName, $source)) {
				return is_dir($source) ? str_replace('\\', '/', substr($fileName, strlen($source) + 1)) : basename($fileName);
			}
		}

		throw new InvalidArgumentException(sprintf('Could not determine "%s" relative path', $fileName));
	}

	/**
	 * Returns template directory.
	 *
	 * @return string
	 */
	private function getTemplateDir()
	{
		return dirname($this->config->templateConfig);
	}

	/**
	 * Returns template path.
	 *
	 * @param string $name Template name
	 * @param string $type Template type
	 * @return string
	 */
	private function getTemplatePath($name, $type = 'main')
	{
		return $this->getTemplateDir() . DIRECTORY_SEPARATOR . $this->config->template->templates->$type->$name->template;
	}

	/**
	 * Returns template filename.
	 *
	 * @param string $name Template name
	 * @param string $type Template type
	 * @return string
	 */
	private function getTemplateFileName($name, $type = 'main')
	{
		return $this->config->destination . DIRECTORY_SEPARATOR . $this->config->template->templates->$type->$name->filename;
	}

	/**
	 * Checks if template exists.
	 *
	 * @param string $name Template name
	 * @param string $type Template type
	 * @return string
	 */
	private function templateExists($name, $type = 'main')
	{
		return isset($this->config->template->templates->$type->$name);
	}

	/**
	 * Checks if template exists and creates dir.
	 *
	 * @param string $name
	 * @throws \RuntimeException If template is not set.
	 */
	private function prepareTemplate($name)
	{
		if (!$this->templateExists($name)) {
			throw new RuntimeException(sprintf('Template for "%s" is not set', $name));
		}

		$this->forceDir($this->getTemplateFileName($name));
		return $this;
	}

	/**
	 * Returns list of all generated files.
	 *
	 * @return array
	 */
	private function getGeneratedFiles()
	{
		$files = array();

		// Resources
		foreach ($this->config->template->resources as $item) {
			$path = $this->getTemplateDir() . DIRECTORY_SEPARATOR . $item;
			if (is_dir($path)) {
				$iterator = Nette\Utils\Finder::findFiles('*')->from($path)->getIterator();
				foreach ($iterator as $innerItem) {
					$files[] = $this->config->destination . DIRECTORY_SEPARATOR . $item . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
				}
			} else {
				$files[] = $this->config->destination . DIRECTORY_SEPARATOR . $item;
			}
		}

		// Common files
		foreach ($this->config->template->templates->common as $item) {
			$files[] = $this->config->destination . DIRECTORY_SEPARATOR . $item;
		}

		// Optional files
		foreach ($this->config->template->templates->optional as $optional) {
			$files[] = $this->config->destination . DIRECTORY_SEPARATOR . $optional['filename'];
		}

		// Main files
		$masks = array_map(function($config) {
			return preg_replace('~%[^%]*?s~', '*', $config['filename']);
		}, $this->config->template->templates->main->toArray());
		$filter = function($item) use ($masks) {
			foreach ($masks as $mask) {
				if (fnmatch($mask, $item->getFilename())) {
					return true;
				}
			}
			return false;
		};

		foreach (Nette\Utils\Finder::findFiles('*')->filter($filter)->from($this->config->destination) as $item) {
			$files[] = $item->getPathName();
		}

		return $files;
	}

	/**
	 * Ensures a directory is created.
	 *
	 * @param string $path Directory path
	 * @return string
	 */
	private function forceDir($path)
	{
		@mkdir(dirname($path), 0755, true);
		return $path;
	}

	/**
	 * Deletes a directory.
	 *
	 * @param string $path Directory path
	 * @return boolean
	 */
	private function deleteDir($path)
	{
		if (!is_dir($path)) {
			return true;
		}

		foreach (Nette\Utils\Finder::find('*')->from($path)->childFirst() as $item) {
			if ($item->isDir()) {
				if (!@rmdir($item)) {
					return false;
				}
			} elseif ($item->isFile()) {
				if (!@unlink($item)) {
					return false;
				}
			}
		}
		if (!@rmdir($path)) {
			return false;
		}

		return true;
	}
}

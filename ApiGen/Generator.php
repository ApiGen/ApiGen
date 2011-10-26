<?php

/**
 * ApiGen 2.2.1 - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

use Nette, FSHL;
use TokenReflection\Broker;

/**
 * Generates a HTML API documentation.
 *
 * @author Jaroslav Hanslík
 * @author Ondřej Nešpor
 * @author David Grudl
 */
class Generator extends Nette\Object
{
	/**
	 * Library name.
	 *
	 * @var string
	 */
	const NAME = 'ApiGen';

	/**
	 * Library version.
	 *
	 * @var string
	 */
	const VERSION = '2.2.1';

	/**
	 * Configuration.
	 *
	 * @var \ApiGen\Config
	 */
	private $config;

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
	 * Progressbar settings and status.
	 *
	 * @var array
	 */
	private $progressbar = array(
		'skeleton' => '[%s] %\' 6.2f%%',
		'width' => 80,
		'bar' => 70,
		'current' => 0,
		'maximum' => 1
	);

	/**
	 * Sets configuration.
	 *
	 * @param array $config
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->parsedClasses = new \ArrayObject();
	}

	/**
	 * Scans and parses PHP files.
	 *
	 * @return array
	 * @throws \ApiGen\Exception If no PHP files have been found.
	 */
	public function parse()
	{
		$files = array();

		$flags = \RecursiveDirectoryIterator::CURRENT_AS_FILEINFO | \RecursiveDirectoryIterator::SKIP_DOTS;
		if (defined('\RecursiveDirectoryIterator::FOLLOW_SYMLINKS')) {
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
			} else {
				$entries[] = new \SplFileInfo($source);
			}

			foreach ($entries as $entry) {
				if (!preg_match('~\\.php$~i', $entry->getFilename())) {
					continue;
				}
				foreach ($this->config->exclude as $mask) {
					if (fnmatch($mask, $entry->getPathName(), FNM_NOESCAPE)) {
						continue 2;
					}
				}

				$files[$entry->getPathName()] = $entry->getSize();
				if ($entry->getPathName() !== $entry->getRealPath()) {
					$this->symlinks[$entry->getRealPath()] = $entry->getPathName();
				}
			}
		}

		if (empty($files)) {
			throw new Exception('No PHP files found.');
		}

		if ($this->config->progressbar) {
			$this->prepareProgressBar(array_sum($files));
		}

		$broker = new Broker(new Backend($this, !empty($this->config->undocumented)), Broker::OPTION_DEFAULT & ~(Broker::OPTION_PARSE_FUNCTION_BODY | Broker::OPTION_SAVE_TOKEN_STREAM));

		foreach ($files as $file => $size) {
			$broker->processFile($file);
			$this->incrementProgressBar($size);
		}

		// Classes
		$this->parsedClasses->exchangeArray($broker->getClasses(Backend::TOKENIZED_CLASSES | Backend::INTERNAL_CLASSES | Backend::NONEXISTENT_CLASSES));
		$this->parsedClasses->uksort('strcasecmp');

		// Constants
		$this->parsedConstants = new \ArrayObject($broker->getConstants());
		$this->parsedConstants->uksort('strcasecmp');

		// Functions
		$this->parsedFunctions = new \ArrayObject($broker->getFunctions());
		$this->parsedFunctions->uksort('strcasecmp');

		$documentedCounter = function($count, $element) {
			return $count += (int) $element->isDocumented();
		};

		return array(
			count($broker->getClasses(Backend::TOKENIZED_CLASSES)),
			count($this->parsedConstants),
			count($this->parsedFunctions),
			count($broker->getClasses(Backend::INTERNAL_CLASSES)),
			array_reduce($broker->getClasses(Backend::TOKENIZED_CLASSES), $documentedCounter),
			array_reduce($this->parsedConstants->getArrayCopy(), $documentedCounter),
			array_reduce($this->parsedFunctions->getArrayCopy(), $documentedCounter),
			array_reduce($broker->getClasses(Backend::INTERNAL_CLASSES), $documentedCounter)
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
		// Temporary directory
		$tmpDir = $this->config->destination . '/tmp';
		if (is_dir($tmpDir) && !$this->deleteDir($tmpDir)) {
			return false;
		}

		// Resources
		foreach ($this->config->template['resources'] as $resource) {
			$path = $this->config->destination . '/' . $resource;
			if (is_dir($path) && !$this->deleteDir($path)) {
				return false;
			} elseif (is_file($path) && !@unlink($path)) {
				return false;
			}
		}

		// Common files
		$filenames = array_keys($this->config->template['templates']['common']);
		foreach (Nette\Utils\Finder::findFiles($filenames)->from($this->config->destination) as $item) {
			if (!@unlink($item)) {
				return false;
			}
		}

		// Optional files
		foreach ($this->config->template['templates']['optional'] as $optional) {
			$file = $this->config->destination . '/' . $optional['filename'];
			if (is_file($file) && !@unlink($file)) {
				return false;
			}
		}

		// Main files
		$masks = array_map(function($config) {
			return preg_replace('~%[^%]*?s~', '*', $config['filename']);
		}, $this->config->template['templates']['main']);
		$filter = function($item) use ($masks) {
			foreach ($masks as $mask) {
				if (fnmatch($mask, $item->getFilename())) {
					return true;
				}
			}

			return false;
		};

		foreach (Nette\Utils\Finder::findFiles('*')->filter($filter)->from($this->config->destination) as $item) {
			if (!@unlink($item)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Generates API documentation.
	 *
	 * @throws \ApiGen\Exception If destination directory is not writable.
	 */
	public function generate()
	{
		@mkdir($this->config->destination, 0755, true);
		if (!is_dir($this->config->destination) || !is_writable($this->config->destination)) {
			throw new Exception(sprintf('Directory %s isn\'t writable.', $this->config->destination));
		}

		// Copy resources
		foreach ($this->config->template['resources'] as $resourceSource => $resourceDestination) {
			// File
			$resourcePath = $this->getTemplateDir() . '/' . $resourceSource;
			if (is_file($resourcePath)) {
				copy($resourcePath, $this->forceDir($this->config->destination . '/' . $resourceDestination));
				continue;
			}

			// Dir
			foreach ($iterator = Nette\Utils\Finder::findFiles('*')->from($resourcePath)->getIterator() as $item) {
				copy($item->getPathName(), $this->forceDir($this->config->destination . '/' . $resourceDestination . '/' . $iterator->getSubPathName()));
			}
		}

		// Categorize by packages and namespaces
		$this->categorize();

		// Prepare progressbar
		if ($this->config->progressbar) {
			$max = count($this->packages)
				+ count($this->namespaces)
				+ count($this->classes)
				+ count($this->interfaces)
				+ count($this->traits)
				+ count($this->exceptions)
				+ count($this->constants)
				+ count($this->functions)
				+ count($this->config->template['templates']['common'])
				+ (int) !empty($this->config->undocumented)
				+ (int) $this->config->tree
				+ (int) $this->config->deprecated
				+ (int) $this->config->todo
				+ (int) $this->isSitemapEnabled()
				+ (int) $this->isOpensearchEnabled()
				+ (int) $this->isRobotsEnabled();

			if ($this->config->sourceCode) {
				$tokenizedFilter = function(ReflectionClass $class) {
					return $class->isTokenized();
				};
				$max += count(array_filter($this->classes, $tokenizedFilter))
					+ count(array_filter($this->interfaces, $tokenizedFilter))
					+ count(array_filter($this->traits, $tokenizedFilter))
					+ count(array_filter($this->exceptions, $tokenizedFilter))
					+ count($this->constants)
					+ count($this->functions);
				unset($tokenizedFilter);
			}

			$this->prepareProgressBar($max);
		}

		// Create temporary directory
		$tmp = $this->config->destination . DIRECTORY_SEPARATOR . 'tmp';
		@mkdir($tmp, 0755, true);

		// Prepare template
		$template = new Template($this);
		$template->setCacheStorage(new Nette\Caching\Storages\PhpFileStorage($tmp));
		$template->generator = self::NAME;
		$template->version = self::VERSION;
		$template->config = $this->config;

		// Common files
		$this->generateCommon($template);

		// Optional files
		$this->generateOptional($template);

		// List of undocumented elements
		if (!empty($this->config->undocumented)) {
			$this->generateUndocumented();
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

				if ($element instanceof ReflectionConstant) {
					$this->constants[$elementName] = $element;
					$this->packages[$packageName]['constants'][$elementName] = $element;
					$this->namespaces[$namespaceName]['constants'][$element->getShortName()] = $element;
				} elseif ($element instanceof ReflectionFunction) {
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

		// Sorting for namespaces and packages
		$main = $this->config->main;
		$sort = function($one, $two) use ($main) {
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
		};

		// Select only packages or namespaces
		$userPackages = count(array_diff(array_keys($this->packages), array('PHP', 'None')));
		$userNamespaces = count(array_diff(array_keys($this->namespaces), array('PHP', 'None')));
		if ($userNamespaces > 0 || 0 === $userPackages) {
			$this->packages = array();

			// Don't generate only 'None' namespace
			if (1 === count($this->namespaces) && isset($this->namespaces['None'])) {
				$this->namespaces = array();
			}

			foreach (array_keys($this->namespaces) as $namespaceName) {
				// Add missing parent namespaces
				$parent = '';
				foreach (explode('\\', $namespaceName) as $part) {
					$parent = ltrim($parent . '\\' . $part, '\\');
					if (!isset($this->namespaces[$parent])) {
						$this->namespaces[$parent] = array('classes' => array(), 'interfaces' => array(), 'traits' => array(), 'exceptions' => array(), 'constants' => array(), 'functions' => array());
					}
				}

				// Add missing element types
				foreach ($this->getElementTypes() as $type) {
					if (!isset($this->namespaces[$namespaceName][$type])) {
						$this->namespaces[$namespaceName][$type] = array();
					}
				}
			}
			uksort($this->namespaces, $sort);
		} else {
			$this->namespaces = array();

			foreach (array_keys($this->packages) as $packageName) {
				// Add missing parent packages
				$parent = '';
				foreach (explode('\\', $packageName) as $part) {
					$parent = ltrim($parent . '\\' . $part, '\\');
					if (!isset($this->packages[$parent])) {
						$this->packages[$parent] = array('classes' => array(), 'interfaces' => array(), 'traits' => array(), 'exceptions' => array(), 'constants' => array(), 'functions' => array());
					}
				}

				// Add missing class types
				foreach ($this->getElementTypes() as $type) {
					if (!isset($this->packages[$packageName][$type])) {
						$this->packages[$packageName][$type] = array();
					}
				}
			}
			uksort($this->packages, $sort);
		}

		return $this;
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

		// Elements for autocomplete
		$elements = array();
		foreach ($this->getElementTypes() as $type) {
			foreach ($this->$type as $element) {
				$type = $element instanceof ReflectionClass ? 'class' : ($element instanceof ReflectionConstant ? 'constant' : 'function');
				$elements[] = array($type, $element->getName());
			}
		}
		usort($elements, function($one, $two) {
			return strcasecmp($one[1], $two[1]);
		});
		$template->elements = $elements;

		foreach ($this->config->template['templates']['common'] as $dest => $source) {
			$template
				->setFile($this->getTemplateDir() . '/' . $source)
				->save($this->forceDir($this->config->destination . '/' . $dest));

			$this->incrementProgressBar();
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
			$this->incrementProgressBar();
		}
		if ($this->isOpensearchEnabled()) {
			$template
				->setFile($this->getTemplatePath('opensearch', 'optional'))
				->save($this->forceDir($this->getTemplateFileName('opensearch', 'optional')));
			$this->incrementProgressBar();
		}
		if ($this->isRobotsEnabled()) {
			$template
				->setFile($this->getTemplatePath('robots', 'optional'))
				->save($this->forceDir($this->getTemplateFileName('robots', 'optional')));
			$this->incrementProgressBar();
		}

		return $this;
	}

	/**
	 * Generates list of undocumented elements.
	 *
	 * @return \ApiGen\Generator
	 * @throws \ApiGen\Exception If file isn't writable.
	 */
	private function generateUndocumented()
	{
		// Function for element labels
		$labeler = function($element) {
			if ($element instanceof ReflectionClass) {
				if ($element->isInterface()) {
					return sprintf('interface %s', $element->getName());
				} elseif ($element->isTrait()) {
					return sprintf('trait %s', $element->getName());
				} elseif ($element->isException()) {
					return sprintf('exception %s', $element->getName());
				} else {
					return sprintf('class %s', $element->getName());
				}
			} elseif ($element instanceof ReflectionMethod) {
				return sprintf('method %s::%s()', $element->getDeclaringClassName(), $element->getName());
			} elseif ($element instanceof ReflectionFunction) {
				return sprintf('function %s()', $element->getName());
			} elseif ($element instanceof ReflectionConstant) {
				if ($className = $element->getDeclaringClassName()) {
					return sprintf('constant %s::%s', $className, $element->getName());
				} else {
					return sprintf('constant %s', $element->getName());
				}
			} elseif ($element instanceof ReflectionProperty) {
				return sprintf('property %s::$%s', $element->getDeclaringClassName(), $element->getName());
			} elseif ($element instanceof ReflectionParameter) {
				if ($element->getDeclaringFunction() instanceof ReflectionMethod) {
					$parentLabel = sprintf('method %s::%s()', $element->getDeclaringClassName(), $element->getDeclaringFunctionName());
				} else {
					$parentLabel = sprintf('function %s()', $element->getDeclaringFunctionName());
				}
				return sprintf('parameter $%s of %s', $element->getName(), $parentLabel);
			}
		};

		$undocumented = array();
		foreach ($this->getElementTypes() as $type) {
			foreach ($this->$type as $parentElement) {
				// Skip elements not from the main project
				if (!$parentElement->isMain()) {
					continue;
				}

				// Internal elements don't have documentation
				if ($parentElement->isInternal()) {
					continue;
				}

				$elements = array($parentElement);
				if ($parentElement instanceof ReflectionClass) {
					$elements = array_merge(
						$elements,
						array_values($parentElement->getOwnMethods()),
						array_values($parentElement->getOwnConstants()),
						array_values($parentElement->getOwnProperties())
					);
				}

				$fileName = $parentElement->getFileName();

				$tokens = $parentElement->getBroker()->getFileTokens($parentElement->getFileName());

				foreach ($elements as $element) {
					$line = $element->getStartLine();
					$label = $labeler($element);

					$annotations = $element->getAnnotations();

					// Documentation
					if (empty($element->shortDescription)) {
						if (empty($annotations)) {
							$undocumented[$fileName][] = array('error', $line, sprintf('Missing documentation of %s', $label));
							continue;
						}
						// Description
						$undocumented[$fileName][] = array('error', $line, sprintf('Missing description of %s', $label));
					}

					// Documentation of method
					if ($element instanceof ReflectionMethod || $element instanceof ReflectionFunction) {
						// Parameters
						foreach ($element->getParameters() as $no => $parameter) {
							if (!isset($annotations['param'][$no])) {
								$undocumented[$fileName][] = array('error', $line, sprintf('Missing documentation of %s', $labeler($parameter)));
								continue;
							}

							if (!preg_match('~^[\\w\\\\]+(?:\\|[\\w\\\\]+)*\\s+\$' . $parameter->getName() . '(?:\\s+.+)?$~s', $annotations['param'][$no])) {
								$undocumented[$fileName][] = array('warning', $line, sprintf('Invalid documentation "%s" of %s', $annotations['param'][$no], $labeler($parameter)));
							}

							unset($annotations['param'][$no]);
						}
						if (isset($annotations['param'])) {
							foreach ($annotations['param'] as $annotation) {
								$undocumented[$fileName][] = array('warning', $line, sprintf('Existing documentation "%s" of nonexistent parameter of %s', $annotation, $label));
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
							$undocumented[$fileName][] = array('error', $line, sprintf('Missing documentation of return value of %s', $label));
						} elseif (isset($annotations['return'])) {
							if (!$return && 'void' !== $annotations['return'][0] && ($element instanceof ReflectionFunction || (!$parentElement->isInterface() && !$element->isAbstract()))) {
								$undocumented[$fileName][] = array('warning', $line, sprintf('Existing documentation "%s" of nonexistent return value of %s', $annotations['return'][0], $label));
							} elseif (!preg_match('~^[\\w\\\\]+(?:\\|[\\w\\\\]+)*(?:\\s+.+)?$~s', $annotations['return'][0])) {
								$undocumented[$fileName][] = array('warning', $line, sprintf('Invalid documentation "%s" of return value of %s', $annotations['return'][0], $label));
							}
						}
						if (isset($annotations['return'][1])) {
							$undocumented[$fileName][] = array('warning', $line, sprintf('Duplicate documentation "%s" of return value of %s', $annotations['return'][1], $label));
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
							$undocumented[$fileName][] = array('error', $line, sprintf('Missing documentation of throwing an exception of %s', $label));
						} elseif (isset($annotations['throws'])	&& !preg_match('~^[\\w\\\\]+(?:\\|[\w\\\\]+)*(?:\\s+.+)?$~s', $annotations['throws'][0])) {
							$undocumented[$fileName][] = array('warning', $line, sprintf('Invalid documentation "%s" of throwing an exception of %s', $annotations['throws'][0], $label));
						}
					}

					// Data type of constants & properties
					if ($element instanceof ReflectionProperty || $element instanceof ReflectionConstant) {
						if (!isset($annotations['var'])) {
							$undocumented[$fileName][] = array('error', $line, sprintf('Missing documentation of the data type of %s', $label));
						} elseif (!preg_match('~^[\\w\\\\]+(?:\\|[\w\\\\]+)*(?:\\s+.+)?$~s', $annotations['var'][0])) {
							$undocumented[$fileName][] = array('warning', $line, sprintf('Invalid documentation "%s" of the data type of %s', $annotations['var'][0], $label));
						}

						if (isset($annotations['var'][1])) {
							$undocumented[$fileName][] = array('warning', $line, sprintf('Duplicate documentation "%s" of the data type of %s', $annotations['var'][1], $label));
						}
					}
				}
				unset($tokens);
			}
		}
		uksort($undocumented, 'strcasecmp');

		$file = @fopen($this->config->undocumented, 'w');
		if (false === $file) {
			throw new Exception(sprintf('File %s isn\'t writable.', $this->config->undocumented));
		}
		fwrite($file, sprintf('<?xml version="1.0" encoding="UTF-8"?>%s', "\n"));
		fwrite($file, sprintf('<checkstyle version="1.3.0">%s', "\n"));
		foreach ($undocumented as $fileName => $reports) {
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

		$this->incrementProgressBar();

		return $this;
	}

	/**
	 * Generates list of deprecated elements.
	 *
	 * @param \ApiGen\Template $template Template
	 * @return \ApiGen\Generator
	 * @throws \ApiGen\Exception If template is not set.
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

		$this->incrementProgressBar();

		return $this;
	}

	/**
	 * Generates list of tasks.
	 *
	 * @param \ApiGen\Template $template Template
	 * @return \ApiGen\Generator
	 * @throws \ApiGen\Exception If template is not set.
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

		$this->incrementProgressBar();

		return $this;
	}

	/**
	 * Generates classes/interfaces/traits/exceptions tree.
	 *
	 * @param \ApiGen\Template $template Template
	 * @return \ApiGen\Generator
	 * @throws \ApiGen\Exception If template is not set.
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

		$this->incrementProgressBar();

		return $this;
	}

	/**
	 * Generates packages summary.
	 *
	 * @param \ApiGen\Template $template Template
	 * @return \ApiGen\Generator
	 * @throws \ApiGen\Exception If template is not set.
	 */
	private function generatePackages(Template $template)
	{
		if (empty($this->packages)) {
			return $this;
		}

		$this->prepareTemplate('package');

		foreach ($this->packages as $packageName => $package) {
			$template->package = $packageName;
			$template->subpackages = array_filter($template->packages, function($subpackageName) use ($packageName) {
				return 0 === strpos($subpackageName, $packageName . '\\');
			});
			$template->classes = $package['classes'];
			$template->interfaces = $package['interfaces'];
			$template->traits = $package['traits'];
			$template->exceptions = $package['exceptions'];
			$template->constants = $package['constants'];
			$template->functions = $package['functions'];
			$template
				->setFile($this->getTemplatePath('package'))
				->save($this->config->destination . '/' . $template->getPackageUrl($packageName));

			$this->incrementProgressBar();
		}
		unset($template->subpackages);

		return $this;
	}

	/**
	 * Generates namespaces summary.
	 *
	 * @param \ApiGen\Template $template Template
	 * @return \ApiGen\Generator
	 * @throws \ApiGen\Exception If template is not set.
	 */
	private function generateNamespaces(Template $template)
	{
		if (empty($this->namespaces)) {
			return $this;
		}

		$this->prepareTemplate('namespace');

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
				->save($this->config->destination . '/' . $template->getNamespaceUrl($namespaceName));

			$this->incrementProgressBar();
		}
		unset($template->subnamespaces);

		return $this;
	}

	/**
	 * Generate classes, interfaces, traits, exceptions, constants and functions files.
	 *
	 * @param Template $template Template
	 * @return \ApiGen\Generator
	 * @throws \ApiGen\Exception If template is not set.
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

			$fshl = new FSHL\Highlighter(new FSHL\Output\Html(), FSHL\Highlighter::OPTION_TAB_INDENT | FSHL\Highlighter::OPTION_LINE_COUNTER);
			$fshl->setLexer(new FSHL\Lexer\Php());
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
				if (!empty($this->packages)) {
					$template->package = $packageName = $element->getPseudoPackageName();
					$template->classes = $this->packages[$packageName]['classes'];
					$template->interfaces = $this->packages[$packageName]['interfaces'];
					$template->traits = $this->packages[$packageName]['traits'];
					$template->exceptions = $this->packages[$packageName]['exceptions'];
					$template->constants = $this->packages[$packageName]['constants'];
					$template->functions = $this->packages[$packageName]['functions'];
				} elseif (!empty($this->namespaces)) {
					$template->namespace = $namespaceName = $element->getPseudoNamespaceName();
					$template->classes = $this->namespaces[$namespaceName]['classes'];
					$template->interfaces = $this->namespaces[$namespaceName]['interfaces'];
					$template->traits = $this->namespaces[$namespaceName]['traits'];
					$template->exceptions = $this->namespaces[$namespaceName]['exceptions'];
					$template->constants = $this->namespaces[$namespaceName]['constants'];
					$template->functions = $this->namespaces[$namespaceName]['functions'];
				}

				$template->fileName = null;
				if ($element->isTokenized()) {
					$template->fileName = $this->getRelativePath($element);
				}

				$template->class = null;
				$template->constant = null;
				$template->function = null;
				if ($element instanceof ReflectionClass) {
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
						->save($this->config->destination . '/' . $template->getClassUrl($element));
				} elseif ($element instanceof ReflectionConstant) {
					// Constant
					$template->constant = $element;

					$template
						->setFile($this->getTemplatePath('constant'))
						->save($this->config->destination . '/' . $template->getConstantUrl($element));
				} elseif ($element instanceof ReflectionFunction) {
					// Function
					$template->function = $element;

					$template
						->setFile($this->getTemplatePath('function'))
						->save($this->config->destination . '/' . $template->getFunctionUrl($element));
				}

				$this->incrementProgressBar();

				// Generate source codes
				if ($this->config->sourceCode && $element->isTokenized()) {
					$template->source = $fshl->highlight(file_get_contents($element->getFileName()));
					$template
						->setFile($this->getTemplatePath('source'))
						->save($this->config->destination . '/' . $template->getSourceUrl($element, false));

					$this->incrementProgressBar();
				}
			}
		}

		return $this;
	}

	/**
	 * Prints message if printing is enabled.
	 *
	 * @param string $message Output message
	 */
	public function output($message)
	{
		if (!$this->config->quiet) {
			echo $this->colorize($message);
		}
	}

	/**
	 * Colorizes message or removes placeholders if OS doesn't support colors.
	 *
	 * @param string $message
	 * @return string
	 */
	public function colorize($message)
	{
		static $placeholders = array(
			'@header@' => "\x1b[1;34m",
			'@count@' => "\x1b[1;34m",
			'@option@' => "\x1b[0;36m",
			'@value@' => "\x1b[0;32m",
			'@error@' => "\x1b[0;31m",
			'@c' => "\x1b[0m"
		);

		if (!$this->config->colors) {
			$placeholders = array_fill_keys(array_keys($placeholders), '');
		}

		return strtr($message, $placeholders);
	}

	/**
	 * Returns header.
	 *
	 * @return string
	 */
	public function getHeader()
	{
		$name = sprintf('%s %s', self::NAME, self::VERSION);
		return sprintf("@header@%s@c\n%s\n", $name, str_repeat('-', strlen($name)));
	}

	/**
	 * Prepares the progressbar.
	 *
	 * @param integer $maximum Maximum progressbar value
	 */
	private function prepareProgressBar($maximum = 1)
	{
		if (!$this->config->progressbar) {
			return;
		}

		$this->progressbar['current'] = 0;
		$this->progressbar['maximum'] = $maximum;
	}

	/**
	 * Increments the progressbar by one.
	 *
	 * @param integer $increment Progressbar increment
	 */
	private function incrementProgressBar($increment = 1)
	{
		if (!$this->config->progressbar) {
			return;
		}

		echo str_repeat(chr(0x08), $this->progressbar['width']);

		$this->progressbar['current'] += $increment;

		$percent = $this->progressbar['current'] / $this->progressbar['maximum'];

		$progress = str_pad(str_pad('>', round($percent * $this->progressbar['bar']), '=', STR_PAD_LEFT), $this->progressbar['bar'], ' ', STR_PAD_RIGHT);

		echo sprintf($this->progressbar['skeleton'], $progress, $percent * 100);

		if ($this->progressbar['current'] === $this->progressbar['maximum']) {
			echo "\n";
		}
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
	 * @param \ApiGen\ReflectionMethod $one
	 * @param \ApiGen\ReflectionMethod $two
	 * @return integer
	 */
	private function sortMethods(ReflectionMethod $one, ReflectionMethod $two)
	{
		return strcasecmp($one->getDeclaringClassName() . '::' . $one->getName(), $two->getDeclaringClassName() . '::' . $two->getName());
	}

	/**
	 * Sorts constants by FQN.
	 *
	 * @param \ApiGen\ReflectionConstant $one
	 * @param \ApiGen\ReflectionConstant $two
	 * @return integer
	 */
	private function sortConstants(ReflectionConstant $one, ReflectionConstant $two)
	{
		return strcasecmp(($one->getDeclaringClassName() ?: $one->getNamespaceName()) . '\\' .  $one->getName(), ($two->getDeclaringClassName() ?: $two->getNamespaceName()) . '\\' .  $two->getName());
	}

	/**
	 * Sorts functions by FQN.
	 *
	 * @param \ApiGen\ReflectionFunction $one
	 * @param \ApiGen\ReflectionFunction $two
	 * @return integer
	 */
	private function sortFunctions(ReflectionFunction $one, ReflectionFunction $two)
	{
		return strcasecmp($one->getNamespaceName() . '\\' . $one->getName(), $two->getNamespaceName() . '\\' . $two->getName());
	}

	/**
	 * Sorts functions by FQN.
	 *
	 * @param \ApiGen\ReflectionProperty $one
	 * @param \ApiGen\ReflectionProperty $two
	 * @return integer
	 */
	private function sortProperties(ReflectionProperty $one, ReflectionProperty $two)
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
	 * Returns element relative path to the source directory.
	 *
	 * @param \ApiGen\ReflectionElement $element
	 * @return string
	 * @throws \ApiGen\Exception If relative path could not be determined.
	 */
	private function getRelativePath(ReflectionElement $element)
	{
		$fileName = $element->getFileName();
		if (isset($this->symlinks[$fileName])) {
			$fileName = $this->symlinks[$fileName];
		}
		foreach ($this->config->source as $source) {
			if (0 === strpos($fileName, $source)) {
				return is_dir($source) ? str_replace('\\', '/', substr($fileName, strlen($source) + 1)) : basename($fileName);
			}
		}

		throw new Exception(sprintf('Could not determine element %s relative path', $element->getName()));
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
		return $this->getTemplateDir() . '/' . $this->config->template['templates'][$type][$name]['template'];
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
		return $this->config->destination . '/' . $this->config->template['templates'][$type][$name]['filename'];
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
		return isset($this->config->template['templates'][$type][$name]);
	}

	/**
	 * Checks if template exists and creates dir.
	 *
	 * @param mixed $name
	 * @throws \ApiGen\Exception If template is not set.
	 */
	private function prepareTemplate($name)
	{
		if (!$this->templateExists($name)) {
			throw new Exception(sprintf('Template for %s is not set.', $name));
		}

		$this->forceDir($this->getTemplateFileName($name));
		return $this;
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

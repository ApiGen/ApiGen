<?php

/**
 * ApiGen 2.0 - API documentation generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;

use Nette, FSHL;
use TokenReflection\Broker;
use TokenReflection\IReflectionProperty as ReflectionProperty, TokenReflection\IReflectionMethod as ReflectionMethod, TokenReflection\IReflectionParameter as ReflectionParameter;
use TokenReflection\ReflectionAnnotation;

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
	const VERSION = '2.0';

	/**
	 * Configuration.
	 *
	 * @var \ApiGen\Config
	 */
	private $config;

	/**
	 * List of classes.
	 *
	 * @var \ArrayObject
	 */
	private $classes = null;

	/**
	 * List of constants.
	 *
	 * @var \ArrayObject
	 */
	private $constants = null;

	/**
	 * List of functions.
	 *
	 * @var \ArrayObject
	 */
	private $functions = null;

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
		$this->classes = new \ArrayObject();
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
					if (fnmatch($mask, $entry->getPathName(), FNM_NOESCAPE | FNM_PATHNAME)) {
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

		$broker = new Broker(new Backend($this, !empty($this->config->undocumented)), false);

		foreach ($files as $file => $size) {
			$broker->processFile($file);
			$this->incrementProgressBar($size);
		}

		// Classes
		$this->classes->exchangeArray($broker->getClasses(Backend::TOKENIZED_CLASSES | Backend::INTERNAL_CLASSES | Backend::NONEXISTENT_CLASSES));
		$this->classes->uksort('strcasecmp');

		// Constants
		$this->constants = new \ArrayObject($broker->getConstants());
		$this->constants->uksort('strcasecmp');

		// Functions
		$this->functions = new \ArrayObject($broker->getFunctions());
		$this->functions->uksort('strcasecmp');

		$documentedCounter = function($count, $element) {
			return $count += (int) $element->isDocumented();
		};

		return array(
			count($broker->getClasses(Backend::TOKENIZED_CLASSES)),
			count($this->constants),
			count($this->functions),
			count($broker->getClasses(Backend::INTERNAL_CLASSES)),
			array_reduce($broker->getClasses(Backend::TOKENIZED_CLASSES), $documentedCounter),
			array_reduce($this->constants->getArrayCopy(), $documentedCounter),
			array_reduce($this->functions->getArrayCopy(), $documentedCounter),
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
	public function getClasses()
	{
		return $this->classes;
	}

	/**
	 * Returns parsed constant list.
	 *
	 * @return \ArrayObject
	 */
	public function getConstants()
	{
		return $this->constants;
	}

	/**
	 * Returns parsed function list.
	 *
	 * @return \ArrayObject
	 */
	public function getFunctions()
	{
		return $this->functions;
	}

	/**
	 * Wipes out the destination directory.
	 *
	 * @return boolean
	 */
	public function wipeOutDestination()
	{
		// Resources
		foreach ($this->config->resources as $resource) {
			$path = $this->config->destination . '/' . $resource;
			if (is_dir($path) && !$this->deleteDir($path)) {
				return false;
			} elseif (is_file($path) && !@unlink($path)) {
				return false;
			}
		}

		// Common files
		$filenames = array_keys($this->config->templates['common']);
		foreach (Nette\Utils\Finder::findFiles($filenames)->from($this->config->destination) as $item) {
			if (!@unlink($item)) {
				return false;
			}
		}

		// Optional files
		foreach ($this->config->templates['optional'] as $optional) {
			$file = $this->config->destination . '/' . $optional['filename'];
			if (is_file($file) && !@unlink($file)) {
				return false;
			}
		}

		// Main files
		$masks = array_map(function($config) {
			return preg_replace('~%[^%]*?s~', '*', $config['filename']);
		}, $this->config->templates['main']);
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
	 * @throws \ApiGen\Exception If destination directory is not writable
	 */
	public function generate()
	{
		@mkdir($this->config->destination, 0755, true);
		if (!is_dir($this->config->destination) || !is_writable($this->config->destination)) {
			throw new Exception(sprintf('Directory %s isn\'t writable.', $this->config->destination));
		}

		$destination = $this->config->destination;
		$templates = $this->config->templates;
		$templatePath = dirname($this->config->templateConfig);

		// Copy resources
		foreach ($this->config->resources as $resourceSource => $resourceDestination) {
			// File
			$resourcePath = $templatePath . '/' . $resourceSource;
			if (is_file($resourcePath)) {
				copy($resourcePath, $this->forceDir($destination . '/' . $resourceDestination));
				continue;
			}

			// Dir
			foreach ($iterator = Nette\Utils\Finder::findFiles('*')->from($resourcePath)->getIterator() as $item) {
				copy($item->getPathName(), $this->forceDir($destination . '/' . $resourceDestination . '/' . $iterator->getSubPathName()));
			}
		}

		// Categorize by packages and namespaces
		$packages = array();
		$namespaces = array();
		$elementTypes = array('classes', 'interfaces', 'exceptions', 'constants', 'functions');
		$classes = array();
		$interfaces = array();
		$exceptions = array();
		$constants = array();
		$functions = array();
		foreach (array('classes', 'constants', 'functions') as $type) {
			foreach ($this->$type as $elementName => $element) {
				if (!$element->isDocumented()) {
					continue;
				}

				$packageName = $element->getPseudoPackageName();
				$namespaceName = $element->getPseudoNamespaceName();

				if ($element instanceof ReflectionConstant) {
					$constants[$elementName] = $element;
					$packages[$packageName]['constants'][$elementName] = $element;
					$namespaces[$namespaceName]['constants'][$element->getShortName()] = $element;
				} elseif ($element instanceof ReflectionFunction) {
					$functions[$elementName] = $element;
					$packages[$packageName]['functions'][$elementName] = $element;
					$namespaces[$namespaceName]['functions'][$element->getShortName()] = $element;
				} elseif ($element->isInterface()) {
					$interfaces[$elementName] = $element;
					$packages[$packageName]['interfaces'][$elementName] = $element;
					$namespaces[$namespaceName]['interfaces'][$element->getShortName()] = $element;
				} elseif ($element->isException()) {
					$exceptions[$elementName] = $element;
					$packages[$packageName]['exceptions'][$elementName] = $element;
					$namespaces[$namespaceName]['exceptions'][$element->getShortName()] = $element;
				} else {
					$classes[$elementName] = $element;
					$packages[$packageName]['classes'][$elementName] = $element;
					$namespaces[$namespaceName]['classes'][$element->getShortName()] = $element;
				}
			}
		}

		// Sorting for namespaces and packages
		$main = $this->config->main;
		$sort = function($a, $b) use ($main) {
			// \ as separator has to be first
			$a = str_replace('\\', ' ', $a);
			$b = str_replace('\\', ' ', $b);

			if ($main) {
				if (0 === strpos($a, $main) && 0 !== strpos($b, $main)) {
					return -1;
				} elseif (0 !== strpos($a, $main) && 0 === strpos($b, $main)) {
					return 1;
				}
			}

			return strcasecmp($a, $b);
		};

		// Select only packages or namespaces
		$userPackages = count(array_diff(array_keys($packages), array('PHP', 'None')));
		$userNamespaces = count(array_diff(array_keys($namespaces), array('PHP', 'None')));
		if ($userNamespaces > 0 || 0 === $userPackages) {
			$packages = array();

			// Don't generate only 'None' namespace
			if (1 === count($namespaces) && isset($namespaces['None'])) {
				$namespaces = array();
			}

			foreach (array_keys($namespaces) as $namespaceName) {
				// Add missing parent namespaces
				$parent = '';
				foreach (explode('\\', $namespaceName) as $part) {
					$parent = ltrim($parent . '\\' . $part, '\\');
					if (!isset($namespaces[$parent])) {
						$namespaces[$parent] = array('classes' => array(), 'interfaces' => array(), 'exceptions' => array(), 'constants' => array(), 'functions' => array());
					}
				}

				// Add missing element types
				foreach ($elementTypes as $type) {
					if (!isset($namespaces[$namespaceName][$type])) {
						$namespaces[$namespaceName][$type] = array();
					}
				}
			}
			uksort($namespaces, $sort);
		} else {
			$namespaces = array();

			foreach (array_keys($packages) as $packageName) {
				// Add missing parent packages
				$parent = '';
				foreach (explode('\\', $packageName) as $part) {
					$parent = ltrim($parent . '\\' . $part, '\\');
					if (!isset($packages[$parent])) {
						$packages[$parent] = array('classes' => array(), 'interfaces' => array(), 'exceptions' => array(), 'constants' => array(), 'functions' => array());
					}
				}

				// Add missing class types
				foreach ($elementTypes as $type) {
					if (!isset($packages[$packageName][$type])) {
						$packages[$packageName][$type] = array();
					}
				}
			}
			uksort($packages, $sort);
		}

		$mainFilter = function($element) {
			return $element->isMain();
		};

		$sitemapEnabled = !empty($this->config->baseUrl) && isset($templates['optional']['sitemap']);
		$opensearchEnabled = !empty($this->config->googleCseId) && !empty($this->config->baseUrl) && isset($templates['optional']['opensearch']);

		if ($this->config->progressbar) {
			$max = count($packages)
				+ count($namespaces)
				+ count($classes)
				+ count($interfaces)
				+ count($exceptions)
				+ count($constants)
				+ count($functions)
				+ count($templates['common'])
				+ (int) !empty($this->config->undocumented)
				+ (int) $this->config->tree
				+ (int) $this->config->deprecated
				+ (int) $this->config->todo
				+ (int) $sitemapEnabled
				+ (int) $opensearchEnabled;

			if ($this->config->sourceCode) {
				$tokenizedFilter = function(ReflectionClass $class) {
					return $class->isTokenized();
				};
				$max += count(array_filter($classes, $tokenizedFilter))
					+ count(array_filter($interfaces, $tokenizedFilter))
					+ count(array_filter($exceptions, $tokenizedFilter))
					+ count($constants)
					+ count($functions);
				unset($tokenizedFilter);
			}

			$this->prepareProgressBar($max);
		}

		// Create tmp directory
		$tmp = $destination . DIRECTORY_SEPARATOR . 'tmp';
		@mkdir($tmp, 0755, true);

		// Prepare template
		$template = new Template($this);
		$template->setCacheStorage(new Nette\Caching\Storages\PhpFileStorage($tmp));
		$template->generator = self::NAME;
		$template->version = self::VERSION;
		$template->config = $this->config;

		// Generate common files
		$template->namespace = null;
		$template->namespaces = array_keys($namespaces);
		$template->package = null;
		$template->packages = array_keys($packages);
		$template->class = null;
		$template->classes = array_filter($classes, $mainFilter);
		$template->interfaces = array_filter($interfaces, $mainFilter);
		$template->exceptions = array_filter($exceptions, $mainFilter);
		$template->constant = null;
		$template->constants = array_filter($constants, $mainFilter);
		$template->function = null;
		$template->functions = array_filter($functions, $mainFilter);

		// Autocomplete
		$elements = array();
		foreach ($elementTypes as $type) {
			foreach ($$type as $element) {
				$type = $element instanceof ReflectionClass ? 'class' : ($element instanceof ReflectionConstant ? 'constant' : 'function');
				$elements[] = array($type, $element->getName());
			}
		}
		usort($elements, function($a, $b) {
			return strcasecmp($a[1], $b[1]);
		});
		$template->elements = $elements;

		foreach ($templates['common'] as $dest => $source) {
			$template->setFile($templatePath . '/' . $source)->save($this->forceDir($destination . '/' . $dest));

			$this->incrementProgressBar();
		}

		unset($elements);
		unset($template->elements);

		// Generate optional files
		if ($sitemapEnabled) {
			$template->setFile($templatePath . '/' . $templates['optional']['sitemap']['template'])->save($this->forceDir($destination . '/' . $templates['optional']['sitemap']['filename']));
			$this->incrementProgressBar();
		}
		if ($opensearchEnabled) {
			$template->setFile($templatePath . '/' . $templates['optional']['opensearch']['template'])->save($this->forceDir($destination . '/' . $templates['optional']['opensearch']['filename']));
			$this->incrementProgressBar();
		}

		// List of undocumented elements
		if (!empty($this->config->undocumented)) {
			$message = function() {
				$args = func_get_args();

				$parentElement = array_shift($args);
				$description = array_pop($args);

				$message = '';
				foreach ($args as $no => $element) {
					if ($parentElement === $element) {
						continue;
					}

					if ($element instanceof ReflectionClass) {
						$label = 'Class %s';
					} elseif ($element instanceof ReflectionMethod) {
						$label = 'Method %s()';
					} elseif ($element instanceof ReflectionFunction) {
						$label = 'Function %s()';
					} elseif ($element instanceof ReflectionConstant) {
						$label = 'Constant %s';
					} elseif ($element instanceof ReflectionProperty) {
						$label = 'Property $%s';
					} elseif ($element instanceof ReflectionParameter) {
						$label = 'Parameter $%s';
					}

					$message .= sprintf($label . ': ', $element->getName());
				}
				return $message . preg_replace('~\s+~', ' ', $description);
			};

			$undocumented = array();
			foreach ($elementTypes as $type) {
				foreach ($$type as $parentElement) {
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
						$elements = array_merge($elements, array_values($parentElement->getOwnMethods()), array_values($parentElement->getOwnConstants()), array_values($parentElement->getOwnProperties()));
					}

					if ($parentElement instanceof ReflectionClass) {
						$parentElementLabel = 'Class %s';
					} elseif ($parentElement instanceof ReflectionConstant) {
						$parentElementLabel = 'Constant %s';
					} else {
						$parentElementLabel = 'Function %s()';
					}
					$parentElementLabel = sprintf($parentElementLabel, $parentElement->getName());

					$tokens = $parentElement->getBroker()->getFileTokens($parentElement->getFileName());

					foreach ($elements as $element) {
						$annotations = $element->getAnnotations();

						// Documentation
						if (empty($annotations)) {
							$undocumented[$parentElementLabel][] = $message($parentElement, $element, 'Missing documentation.');
							continue;
						}

						// Description
						if (!isset($annotations[ReflectionAnnotation::SHORT_DESCRIPTION])) {
							$undocumented[$parentElementLabel][] = $message($parentElement, $element, 'Missing description.');
						}

						// Documentation of method
						if ($element instanceof ReflectionMethod || $element instanceof ReflectionFunction) {
							// Parameters
							foreach ($element->getParameters() as $no => $parameter) {
								if (!isset($annotations['param'][$no])) {
									$undocumented[$parentElementLabel][] = $message($parentElement, $element, $parameter, 'Missing documentation.');
									continue;
								}

								if (!preg_match('~^[\w\\\\]+(?:\|[\w\\\\]+)*\s+\$' . $parameter->getName() . '(?:\s+.+)?$~s', $annotations['param'][$no])) {
									$undocumented[$parentElementLabel][] = $message($parentElement, $element, $parameter, sprintf('Invalid documentation "%s".', $annotations['param'][$no]));
								}

								unset($annotations['param'][$no]);
							}
							if (isset($annotations['param'])) {
								foreach ($annotations['param'] as $annotation) {
									$undocumented[$parentElementLabel][] = $message($parentElement, $element, sprintf('Existing documentation "%s" of nonexistent parameter.', $annotation));
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
								$undocumented[$parentElementLabel][] = $message($parentElement, $element, 'Missing documentation of return value.');
							} elseif (isset($annotations['return'])) {
								if (!$return && 'void' !== $annotations['return'][0] && ($element instanceof ReflectionFunction || (!$parentElement->isInterface() && !$element->isAbstract()))) {
									$undocumented[$parentElementLabel][] = $message($parentElement, $element, sprintf('Existing documentation "%s" of nonexistent return value.', $annotations['return'][0]));
								} elseif (!preg_match('~^[\w\\\\]+(?:\|[\w\\\\]+)*(?:\s+.+)?$~s', $annotations['return'][0])) {
									$undocumented[$parentElementLabel][] = $message($parentElement, $element, sprintf('Invalid documentation "%s" of return value.', $annotations['return'][0]));
								}
							}
							if (isset($annotations['return'][1])) {
								$undocumented[$parentElementLabel][] = $message($parentElement, $element, sprintf('Duplicate documentation "%s" of return value.', $annotations['return'][1]));
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
								$undocumented[$parentElementLabel][] = $message($parentElement, $element, 'Missing documentation of throwing an exception.');
							} elseif (isset($annotations['throws'])	&& !preg_match('~^[\w\\\\]+(?:\|[\w\\\\]+)*(?:\s+.+)?$~s', $annotations['throws'][0])) {
								$undocumented[$parentElementLabel][] = $message($parentElement, $element, sprintf('Invalid documentation "%s" of throwing an exception.', $annotations['throws'][0]));
							}
						}

						// Data type of constants & properties
						if ($element instanceof ReflectionProperty || $element instanceof ReflectionConstant) {
							if (!isset($annotations['var'])) {
								$undocumented[$parentElementLabel][] = $message($parentElement, $element, 'Missing documentation of the data type.');
							} elseif (!preg_match('~^[\w\\\\]+(?:\|[\w\\\\]+)*(?:\s+.+)?$~s', $annotations['var'][0])) {
								$undocumented[$parentElementLabel][] = $message($parentElement, $element, sprintf('Invalid documentation "%s" of the data type.', $annotations['var'][0]));
							}

							if (isset($annotations['var'][1])) {
								$undocumented[$parentElementLabel][] = $message($parentElement, $element, sprintf('Duplicate documentation "%s" of the data type.', $annotations['var'][1]));
							}
						}
					}
					unset($tokens);
				}
			}
			uksort($undocumented, 'strcasecmp');

			$fp = @fopen($this->config->undocumented, 'w');
			if (false === $fp) {
				throw new Exception(sprintf('File %s isn\'t writable.', $this->config->undocumented));
			}
			foreach ($undocumented as $label => $elements) {
				fwrite($fp, sprintf("%s\n%s\n", $label, str_repeat('-', strlen($label))));
				foreach ($elements as $text) {
					fwrite($fp, sprintf("\t%s\n", $text));
				}
				fwrite($fp, "\n");
			}
			fclose($fp);

			$this->incrementProgressBar();

			unset($undocumented);
		}

		// List of deprecated elements
		if ($this->config->deprecated) {
			if (!isset($templates['main']['deprecated'])) {
				throw new Exception('Template for list of deprecated elements is not set');
			}

			$deprecatedFilter = function($element) {
				return $element->isDeprecated();
			};

			$template->deprecatedMethods = array();
			$template->deprecatedConstants = array();
			$template->deprecatedProperties = array();
			foreach (array_reverse($elementTypes) as $type) {
				$template->{'deprecated' . ucfirst($type)} = array_filter(array_filter($$type, $mainFilter), $deprecatedFilter);

				if ('constants' === $type || 'functions' === $type) {
					continue;
				}

				foreach ($$type as $class) {
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
			usort($template->deprecatedMethods, function($a, $b) {
				return strcasecmp($a->getDeclaringClassName() . '::' . $a->getName(), $b->getDeclaringClassName() . '::' . $b->getName());
			});
			usort($template->deprecatedConstants, function($a, $b) {
				return strcasecmp(($a->getDeclaringClassName() ?: $a->getNamespaceName()) . '\\' .  $a->getName(), ($b->getDeclaringClassName() ?: $b->getNamespaceName()) . '\\' .  $b->getName());
			});
			usort($template->deprecatedFunctions, function($a, $b) {
				return strcasecmp($a->getNamespaceName() . '\\' . $a->getName(), $b->getNamespaceName() . '\\' . $b->getName());
			});
			usort($template->deprecatedProperties, function($a, $b) {
				return strcasecmp($a->getDeclaringClassName() . '::' . $a->getName(), $b->getDeclaringClassName() . '::' . $b->getName());
			});

			$template->setFile($templatePath . '/' . $templates['main']['deprecated']['template'])->save($this->forceDir($destination . '/' . $templates['main']['deprecated']['filename']));

			$this->incrementProgressBar();

			unset($deprecatedFilter);
			foreach ($elementTypes as $type) {
				unset($template->{'deprecated' . ucfirst($type)});
			}
			unset($template->deprecatedMethods);
			unset($template->deprecatedProperties);
		}

		// List of tasks
		if ($this->config->todo) {
			if (!isset($templates['main']['todo'])) {
				throw new Exception('Template for list of tasks is not set');
			}

			$todoFilter = function($element) {
				return $element->hasAnnotation('todo');
			};

			$template->todoMethods = array();
			$template->todoConstants = array();
			$template->todoProperties = array();
			foreach (array_reverse($elementTypes) as $type) {
				$template->{'todo' . ucfirst($type)} = array_filter(array_filter($$type, $mainFilter), $todoFilter);

				if ('constants' === $type || 'functions' === $type) {
					continue;
				}

				foreach ($$type as $class) {
					if (!$class->isMain()) {
						continue;
					}

					$template->todoMethods = array_merge($template->todoMethods, array_values(array_filter($class->getOwnMethods(), $todoFilter)));
					$template->todoConstants = array_merge($template->todoConstants, array_values(array_filter($class->getOwnConstants(), $todoFilter)));
					$template->todoProperties = array_merge($template->todoProperties, array_values(array_filter($class->getOwnProperties(), $todoFilter)));
				}
			}
			usort($template->todoMethods, function($a, $b) {
				return strcasecmp($a->getDeclaringClassName() . '::' . $a->getName(), $b->getDeclaringClassName() . '::' . $b->getName());
			});
			usort($template->todoConstants, function($a, $b) {
				return strcasecmp(($a->getDeclaringClassName() ?: $a->getNamespaceName()) . '\\' .  $a->getName(), ($b->getDeclaringClassName() ?: $b->getNamespaceName()) . '\\' .  $b->getName());
			});
			usort($template->todoFunctions, function($a, $b) {
				return strcasecmp($a->getNamespaceName() . '\\' . $a->getName(), $b->getNamespaceName() . '\\' . $b->getName());
			});
			usort($template->todoProperties, function($a, $b) {
				return strcasecmp($a->getDeclaringClassName() . '::' . $a->getName(), $b->getDeclaringClassName() . '::' . $b->getName());
			});

			$template->setFile($templatePath . '/' . $templates['main']['todo']['template'])->save($this->forceDir($destination . '/' . $templates['main']['todo']['filename']));

			$this->incrementProgressBar();

			unset($todoFilter);
			foreach ($elementTypes as $type) {
				unset($template->{'todo' . ucfirst($type)});
			}
			unset($template->todoMethods);
			unset($template->todoProperties);
		}

		// Classes/interfaces/exceptions tree
		if ($this->config->tree) {
			if (!isset($templates['main']['tree'])) {
				throw new Exception('Template for tree view is not set');
			}

			$classTree = array();
			$interfaceTree = array();
			$exceptionTree = array();

			$processed = array();
			foreach ($this->classes as $className => $reflection) {
				if (!$reflection->isMain() || !$reflection->isDocumented() || isset($processed[$className])) {
					continue;
				}

				if (null === $reflection->getParentClassName()) {
					// No parent classes
					if ($reflection->isInterface()) {
						$t = &$interfaceTree;
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

			$template->classTree = new Tree($classTree, $this->classes);
			$template->interfaceTree = new Tree($interfaceTree, $this->classes);
			$template->exceptionTree = new Tree($exceptionTree, $this->classes);

			$template->setFile($templatePath . '/' . $templates['main']['tree']['template'])->save($this->forceDir($destination . '/' . $templates['main']['tree']['filename']));

			unset($template->classTree);
			unset($template->interfaceTree);
			unset($template->exceptionTree);
			unset($processed);

			$this->incrementProgressBar();
		}

		// Generate package summary
		if (!empty($packages)) {
			if (!isset($templates['main']['package'])) {
				throw new Exception('Template for package is not set');
			}

			$this->forceDir($destination . '/' . $templates['main']['package']['filename']);
		}
		foreach ($packages as $packageName => $package) {
			$template->package = $packageName;
			$template->subpackages = array_filter($template->packages, function($subpackageName) use ($packageName) {
				return 0 === strpos($subpackageName, $packageName . '\\');
			});
			$template->classes = $package['classes'];
			$template->interfaces = $package['interfaces'];
			$template->exceptions = $package['exceptions'];
			$template->constants = $package['constants'];
			$template->functions = $package['functions'];
			$template->setFile($templatePath . '/' . $templates['main']['package']['template'])->save($destination . '/' . $template->getPackageUrl($packageName));

			$this->incrementProgressBar();
		}
		unset($template->subpackages);

		// Generate namespace summary
		if (!empty($namespaces)) {
			if (!isset($templates['main']['namespace'])) {
				throw new Exception('Template for namespace is not set');
			}

			$this->forceDir($destination . '/' . $templates['main']['namespace']['filename']);
		}
		foreach ($namespaces as $namespaceName => $namespace) {
			$template->namespace = $namespaceName;
			$template->subnamespaces = array_filter($template->namespaces, function($subnamespaceName) use ($namespaceName) {
				return (bool) preg_match('~^' . preg_quote($namespaceName) . '\\\\[^\\\\]+$~', $subnamespaceName);
			});
			$template->classes = $namespace['classes'];
			$template->interfaces = $namespace['interfaces'];
			$template->exceptions = $namespace['exceptions'];
			$template->constants = $namespace['constants'];
			$template->functions = $namespace['functions'];
			$template->setFile($templatePath . '/' . $templates['main']['namespace']['template'])->save($destination . '/' . $template->getNamespaceUrl($namespaceName));

			$this->incrementProgressBar();
		}
		unset($template->subnamespaces);

		// Generate class & interface & exception files
		$fshl = new FSHL\Highlighter(new FSHL\Output\Html(), FSHL\Highlighter::OPTION_TAB_INDENT | FSHL\Highlighter::OPTION_LINE_COUNTER);
		$fshlPhpLexer = new FSHL\Lexer\Php();
		if (!empty($classes) || !empty($interfaces) || !empty($exceptions)) {
			if (!isset($templates['main']['class'])) {
				throw new Exception('Template for class is not set');
			}

			$this->forceDir($destination . '/' . $templates['main']['class']['filename']);
		}
		if (!empty($constants)) {
			if (!isset($templates['main']['constant'])) {
				throw new Exception('Template for constant is not set');
			}

			$this->forceDir($destination . '/' . $templates['main']['constant']['filename']);
		}
		if (!empty($functions)) {
			if (!isset($templates['main']['function'])) {
				throw new Exception('Template for function is not set');
			}

			$this->forceDir($destination . '/' . $templates['main']['function']['filename']);
		}
		if ($this->config->sourceCode) {
			if (!isset($templates['main']['source'])) {
				throw new Exception('Template for source code is not set');
			}

			$this->forceDir($destination . '/' . $templates['main']['source']['filename']);
		}
		$template->package = null;
		$template->namespace = null;
		$template->classes = $classes;
		$template->interfaces = $interfaces;
		$template->exceptions = $exceptions;
		$template->constants = $constants;
		$template->functions = $functions;
		foreach ($elementTypes as $type) {
			foreach ($$type as $element) {
				if ($element->isTokenized()) {
					$template->fileName = null;
					$fileName = $element->getFileName();
					if (isset($this->symlinks[$fileName])) {
						$fileName = $this->symlinks[$fileName];
					}
					foreach ($this->config->source as $source) {
						if (0 === strpos($fileName, $source)) {
							$template->fileName = is_dir($source) ? str_replace('\\', '/', substr($fileName, strlen($source) + 1)) : basename($fileName);
							break;
						}
					}
					if (null === $template->fileName) {
						throw new Exception(sprintf('Could not determine element %s relative path', $element->getName()));
					}
				}

				if ($packages) {
					$template->package = $packageName = $element->getPseudoPackageName();
					$template->classes = $packages[$packageName]['classes'];
					$template->interfaces = $packages[$packageName]['interfaces'];
					$template->exceptions = $packages[$packageName]['exceptions'];
					$template->constants = $packages[$packageName]['constants'];
					$template->functions = $packages[$packageName]['functions'];
				} elseif ($namespaces) {
					$template->namespace = $namespaceName = $element->getPseudoNamespaceName();
					$template->classes = $namespaces[$namespaceName]['classes'];
					$template->interfaces = $namespaces[$namespaceName]['interfaces'];
					$template->exceptions = $namespaces[$namespaceName]['exceptions'];
					$template->constants = $namespaces[$namespaceName]['constants'];
					$template->functions = $namespaces[$namespaceName]['functions'];
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

					$template->ownMethods = $element->getOwnMethods();
					$template->ownConstants = $element->getOwnConstants();
					$template->ownProperties = $element->getOwnProperties();

					$template->class = $element;

					$template->setFile($templatePath . '/' . $templates['main']['class']['template'])->save($destination . '/' . $template->getClassUrl($element));
				} elseif ($element instanceof ReflectionConstant) {
					// Constant
					$template->constant = $element;

					$template->setFile($templatePath . '/' . $templates['main']['constant']['template'])->save($destination . '/' . $template->getConstantUrl($element));
				} elseif ($element instanceof ReflectionFunction) {
					// Function
					$template->function = $element;

					$template->setFile($templatePath . '/' . $templates['main']['function']['template'])->save($destination . '/' . $template->getFunctionUrl($element));
				}

				$this->incrementProgressBar();

				// Generate source codes
				if ($this->config->sourceCode && $element->isTokenized()) {
					$template->source = $fshl->highlight($fshlPhpLexer, file_get_contents($element->getFileName()));
					$template->setFile($templatePath . '/' . $templates['main']['source']['template'])->save($destination . '/' . $template->getSourceUrl($element, false));

					$this->incrementProgressBar();
				}
			}
			unset($$type);
		}

		// Delete tmp directory
		$this->deleteDir($tmp);
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
			'@header@' => "\x1b[1;34;40m",
			'@count@' => "\x1b[1;34;40m",
			'@option@' => "\x1b[36;40m",
			'@value@' => "\x1b[32;40m",
			'@error@' => "\x1b[31;40m",
			'@c' => "\x1b[0m"
		);

		// Windows doesn't support colors
		if ('WIN' === substr(PHP_OS, 0, 3)) {
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

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

use Nette;
use Apigen\Exception, Apigen\Config, Apigen\Template, Apigen\Backend;
use TokenReflection\Broker;
use Apigen\Reflection as ReflectionClass, TokenReflection\IReflectionProperty as ReflectionProperty, TokenReflection\IReflectionMethod as ReflectionMethod, TokenReflection\IReflectionConstant as ReflectionConstant, TokenReflection\IReflectionFunction as ReflectionFunction, TokenReflection\IReflectionParameter as ReflectionParameter;
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
	const NAME = 'TR ApiGen';

	/**
	 * Library version.
	 *
	 * @var string
	 */
	const VERSION = '2.0 beta 3';

	/**
	 * Configuration.
	 *
	 * @var \Apigen\Config
	 */
	private $config;

	/**
	 * Progressbar
	 *
	 * @var \Console_ProgressBar
	 */
	private $progressBar;

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
	 */
	public function parse()
	{
		$broker = new Broker(new Backend($this), false);

		$files = array();
		foreach ($this->config->source as $source) {
			$entries = array();
			if (is_dir($source)) {
				foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source)) as $entry) {
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
			}
		}

		if (empty($files)) {
			throw new Exception("No PHP files found.");
		}

		if ($this->config->progressbar) {
			$this->prepareProgressBar(array_sum($files));
		}

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

		return array(
			count($broker->getClasses(Backend::TOKENIZED_CLASSES)),
			count($broker->getConstants()),
			count($broker->getFunctions()),
			count($broker->getClasses(Backend::INTERNAL_CLASSES))
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
	 */
	public function generate()
	{
		@mkdir($this->config->destination);
		if (!is_dir($this->config->destination) || !is_writable($this->config->destination)) {
			throw new Exception(sprintf('Directory %s isn\'t writable.', $this->config->destination));
		}

		$destination = $this->config->destination;
		$templates = $this->config->templates;
		$templatePath = $this->config->templateDir . '/' . $this->config->template;

		// Copy resources
		foreach ($this->config->resources as $resourceSource => $resourceDestination) {
			// File
			$resourcePath = $templatePath . '/' . $resourceSource;
			if (is_file($resourcePath)) {
				copy($resourcePath, $this->forceDir("$destination/$resourceDestination"));
				continue;
			}

			// Dir
			foreach ($iterator = Nette\Utils\Finder::findFiles('*')->from($resourcePath)->getIterator() as $item) {
				copy($item->getPathName(), $this->forceDir("$destination/$resourceDestination/" . $iterator->getSubPathName()));
			}
		}

		// Categorize by packages and namespaces
		$packages = array();
		$namespaces = array();
		$elementTypes = array('classes', 'interfaces', 'exceptions', 'constants', 'functions');
		$classes = array();
		$interfaces = array();
		$exceptions = array();
		$constants = $this->constants->getArrayCopy();
		$functions = $this->functions->getArrayCopy();
		foreach ($this->classes as $className => $class) {
			if ($class->isDocumented()) {
				$packageName = $this->getElementPackageName($class);
				$namespaceName = $this->getElementNamespaceName($class);

				if ($class->isInterface()) {
					$interfaces[$className] = $class;
					$packages[$packageName]['interfaces'][$className] = $class;
					$namespaces[$namespaceName]['interfaces'][$class->getShortName()] = $class;
				} elseif ($class->isException()) {
					$exceptions[$className] = $class;
					$packages[$packageName]['exceptions'][$className] = $class;
					$namespaces[$namespaceName]['exceptions'][$class->getShortName()] = $class;
				} else {
					$classes[$className] = $class;
					$packages[$packageName]['classes'][$className] = $class;
					$namespaces[$namespaceName]['classes'][$class->getShortName()] = $class;
				}
			}
		}
		foreach ($constants as $constantName => $constant) {
			$packageName = $this->getElementPackageName($constant);
			$namespaceName = $this->getElementNamespaceName($constant);

			$packages[$packageName]['constants'][$constantName] = $constant;
			$namespaces[$namespaceName]['constants'][$constant->getShortName()] = $constant;
		}
		foreach ($functions as $functionName => $function) {
			$packageName = $this->getElementPackageName($function);
			$namespaceName = $this->getElementNamespaceName($function);

			$packages[$packageName]['functions'][$functionName] = $function;
			$namespaces[$namespaceName]['functions'][$function->getShortName()] = $function;
		}

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
			uksort($namespaces, 'strcasecmp');
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
			uksort($packages, function($a, $b) {
				return strcasecmp(str_replace('\\', ' ', $a), str_replace('\\', ' ', $b));
			});
		}

		$sitemapEnabled = !empty($this->config->baseUrl) && isset($templates['optional']['sitemap']);
		$opensearchEnabled = !empty($this->config->googleCse) && !empty($this->config->baseUrl) && isset($templates['optional']['opensearch']);
		$autocompleteEnabled = !empty($this->config->googleCse) && isset($templates['optional']['autocomplete']);

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
				+ (int) $opensearchEnabled
				+ (int) $autocompleteEnabled
			;

			if ($this->config->sourceCode) {
				$tokenizedFilter = function(ReflectionClass $class) {return $class->isTokenized();};
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
		$template->setCacheStorage(new Nette\Templating\PhpFileStorage($tmp));
		$template->generator = self::NAME;
		$template->version = self::VERSION;
		$template->config = $this->config;

		// Generate summary files
		$template->namespace = null;
		$template->namespaces = array_keys($namespaces);
		$template->package = null;
		$template->packages = array_keys($packages);
		$template->class = null;
		$template->classes = $classes;
		$template->interfaces = $interfaces;
		$template->exceptions = $exceptions;
		$template->constant = null;
		$template->constants = $constants;
		$template->function = null;
		$template->functions = $functions;
		foreach ($templates['common'] as $dest => $source) {
			$template->setFile($templatePath . '/' . $source)->save($this->forceDir("$destination/$dest"));

			$this->incrementProgressBar();
		}

		// Optional files
		if ($sitemapEnabled) {
			$template->setFile($templatePath . '/' . $templates['optional']['sitemap']['template'])->save($this->forceDir($destination . '/' . $templates['optional']['sitemap']['filename']));
			$this->incrementProgressBar();
		}
		if ($opensearchEnabled) {
			$template->setFile($templatePath . '/' . $templates['optional']['opensearch']['template'])->save($this->forceDir($destination . '/' . $templates['optional']['opensearch']['filename']));
			$this->incrementProgressBar();
		}
		if ($autocompleteEnabled) {
			$template->elements = array_keys(array_merge($classes, $interfaces, $exceptions, $constants, $functions));
			usort($template->elements, 'strcasecmp');

			$template->setFile($templatePath . '/' . $templates['optional']['autocomplete']['template'])->save($this->forceDir($destination . '/' . $templates['optional']['autocomplete']['filename']));
			$this->incrementProgressBar();

			unset($template->elements);
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
					// Check only "documented" classes (except internal - no documentation), constants and functions
					if ($parentElement instanceof ReflectionClass && (!$parentElement->isDocumented() || $parentElement->isInternal())) {
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

			$deprecatedFilter = function($element) {return $element->isDeprecated();};

			$template->deprecatedMethods = array();
			$template->deprecatedConstants = array();
			$template->deprecatedProperties = array();
			foreach (array_reverse($elementTypes) as $type) {
				$template->{'deprecated' . ucfirst($type)} = array_filter($$type, $deprecatedFilter);

				if ('constants' === $type || 'functions' === $type) {
					continue;
				}

				foreach ($$type as $class) {
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

			$todoFilter = function($element) {return $element->hasAnnotation('todo');};

			$template->todoMethods = array();
			$template->todoConstants = array();
			$template->todoProperties = array();
			foreach (array_reverse($elementTypes) as $type) {
				$template->{'todo' . ucfirst($type)} = array_filter($$type, $todoFilter);

				if ('constants' === $type || 'functions' === $type) {
					continue;
				}

				foreach ($$type as $class) {
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
				if (!$reflection->isDocumented() || isset($processed[$className])) {
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
		$fshl = new \fshlParser('HTML_UTF8', P_TAB_INDENT | P_LINE_COUNTER);
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
					$file = $element->getFileName();
					foreach ($this->config->source as $source) {
						if (0 === strpos($file, $source)) {
							$template->fileName = is_dir($source) ? str_replace('\\', '/', substr($file, strlen($source) + 1)) : basename($file);
							break;
						}
					}
					if (null === $template->fileName) {
						throw new Exception(sprintf('Could not determine element %s relative path', $element->getName()));
					}
				}

				if ($packages) {
					$template->package = $packageName = $this->getElementPackageName($element);
					$template->classes = $packages[$packageName]['classes'];
					$template->interfaces = $packages[$packageName]['interfaces'];
					$template->exceptions = $packages[$packageName]['exceptions'];
					$template->constants = $packages[$packageName]['constants'];
					$template->functions = $packages[$packageName]['functions'];
				} elseif ($namespaces) {
					$template->namespace = $namespaceName = $this->getElementNamespaceName($element);
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
					$source = file_get_contents($element->getFileName());
					$source = str_replace(array("\r\n", "\r"), "\n", $source);

					$template->source = $fshl->highlightString('PHP', $source);
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
			echo $message;
		}
	}

	/**
	 * Returns header.
	 *
	 * @return string
	 */
	public static function getHeader()
	{
		$name = sprintf('%s %s', self::NAME, self::VERSION);
		return $name . "\n" . str_repeat('-', strlen($name)) . "\n";
	}

	/**
	 * Prepares the progressbar.
	 *
	 * @param $maximum Maximum progressbar value
	 */
	private function prepareProgressBar($maximum = 1)
	{
		$this->progressBar = new \Console_ProgressBar(
			'[%bar%] %percent%',
			'=>',
			' ',
			80,
			$maximum
		);
	}

	/**
	 * Increments the progressbar by one.
	 *
	 * @param integer $increment Progressbar increment
	 */
	private function incrementProgressBar($increment = 1)
	{
		if ($this->config->progressbar) {
			$this->progressBar->update($this->progressBar->getProgress() + $increment);
		}
	}

	/**
	 * Ensures a directory is created.
	 *
	 * @param string Directory path
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

	/**
	 * Returns element package name (including subpackage name).
	 *
	 * For internal elements returns "PHP", for elements in global space returns "None".
	 *
	 * @param \Apigen\Reflection|\TokenReflection\IReflection $element
	 * @return string
	 */
	private function getElementPackageName($element)
	{
		if ($element->isInternal()) {
			$packageName = 'PHP';
		} elseif ($package = $element->getAnnotation('package')) {
			$packageName = $package[0];
			if ($subpackage = $element->getAnnotation('subpackage')) {
				$packageName .= '\\' . $subpackage[0];
			}
		} else {
			$packageName = 'None';
		}

		return $packageName;
	}

	/**
	 * Returns element namespace name.
	 *
	 * For internal elements returns "PHP", for elements in global space returns "None".
	 *
	 * @param \Apigen\Reflection|\TokenReflection\IReflection $element
	 * @return string
	 */
	private function getElementNamespaceName($element)
	{
		return $element->isInternal() ? 'PHP' : $element->getNamespaceName() ?: 'None';
	}
}

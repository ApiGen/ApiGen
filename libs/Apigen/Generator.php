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
use Apigen\Reflection as ApiReflection, Apigen\Exception, Apigen\Config, Apigen\Template, Apigen\Backend;
use TokenReflection\Broker;
use TokenReflection\IReflectionClass as ReflectionClass, TokenReflection\IReflectionProperty as ReflectionProperty, TokenReflection\IReflectionMethod as ReflectionMethod, TokenReflection\IReflectionConstant as ReflectionConstant, TokenReflection\IReflectionParameter as ReflectionParameter;
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
	const VERSION = '2.0 beta 2';

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
	 * Array of reflection envelopes.
	 *
	 * @var \ArrayObject
	 */
	private $classes = array();

	/**
	 * Sets configuration.
	 *
	 * @param array $config
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	/**
	 * Scans and parses PHP files.
	 *
	 * @return array
	 */
	public function parse()
	{
		$broker = new Broker(new Backend(), false);

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

		$this->classes = new \ArrayObject();
		foreach ($broker->getClasses(Backend::TOKENIZED_CLASSES | Backend::INTERNAL_CLASSES | Backend::NONEXISTENT_CLASSES) as $className => $class) {
			$this->classes->offsetSet($className, new ApiReflection($class, $this));
		}
		$this->classes->uksort('strcasecmp');

		return array(
			count($broker->getClasses(Backend::TOKENIZED_CLASSES)),
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
		$classTypes = array('classes', 'interfaces', 'exceptions');
		$classes = array();
		$interfaces = array();
		$exceptions = array();
		foreach ($this->classes as $className => $class) {
			if ($class->isDocumented()) {
				$packageName = $class->isInternal() ? 'PHP' : $class->getPackageName() ?: 'None';
				$namespaceName = $class->isInternal() ? 'PHP' : $class->getNamespaceName() ?: 'None';

				$packages[$packageName]['namespaces'][$namespaceName] = true;
				$namespaces[$namespaceName]['packages'][$packageName] = true;

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

		foreach (array_keys($packages) as $packageName) {
			// Add missing class types
			foreach ($classTypes as $type) {
				if (!isset($packages[$packageName][$type])) {
					$packages[$packageName][$type] = array();
				}
			}
			// Sort namespaces
			uksort($packages[$packageName]['namespaces'], 'strcasecmp');
		}
		uksort($packages, 'strcasecmp');

		foreach (array_keys($namespaces) as $namespaceName) {
			// Add missing parent namespaces
			$parent = '';
			foreach (explode('\\', $namespaceName) as $part) {
				$parent = ltrim($parent . '\\' . $part, '\\');
				if (!isset($namespaces[$parent])) {
					$namespaces[$parent] = array('classes' => array(), 'interfaces' => array(), 'exceptions' => array(), 'packages' => array());
				}
			}

			// Add missing class types
			foreach ($classTypes as $type) {
				if (!isset($namespaces[$namespaceName][$type])) {
					$namespaces[$namespaceName][$type] = array();
				}
			}
			// Sort packages
			uksort($namespaces[$namespaceName]['packages'], 'strcasecmp');
		}
		uksort($namespaces, 'strcasecmp');

		$undocumentedEnabled = !empty($this->config->undocumented);
		$deprecatedEnabled = $this->config->deprecated && isset($templates['optional']['deprecated']);
		$todoEnabled = $this->config->todo && isset($templates['optional']['todo']);
		$sitemapEnabled = !empty($this->config->baseUrl) && isset($templates['optional']['sitemap']);
		$opensearchEnabled = !empty($this->config->googleCse) && !empty($this->config->baseUrl) && isset($templates['optional']['opensearch']);
		$autocompleteEnabled = !empty($this->config->googleCse) && isset($templates['optional']['autocomplete']);

		if ($this->config->progressbar) {
			$max = count($packages)
				+ count($namespaces)
				+ count($classes)
				+ count($interfaces)
				+ count($exceptions)
				+ count($templates['common'])
				+ (int) $undocumentedEnabled
				+ (int) $deprecatedEnabled
				+ (int) $todoEnabled
				+ (int) $sitemapEnabled
				+ (int) $opensearchEnabled
				+ (int) $autocompleteEnabled
				+ 1 // Classes, iterators and exceptions tree
			;

			if ($this->config->sourceCode) {
				$tokenizedFilter = function(ApiReflection $class) {return $class->isTokenized();};
				$max += count(array_filter($classes, $tokenizedFilter))
					+ count(array_filter($interfaces, $tokenizedFilter))
					+ count(array_filter($exceptions, $tokenizedFilter));
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
		$template->deprecated = $deprecatedEnabled;
		$template->todo = $todoEnabled;

		// Generate summary files
		$template->namespace = null;
		$template->namespaces = array_keys($namespaces);
		$template->package = null;
		$template->packages = array_keys($packages);
		$template->class = null;
		$template->classes = $classes;
		$template->interfaces = $interfaces;
		$template->exceptions = $exceptions;
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
			$template->setFile($templatePath . '/' . $templates['optional']['autocomplete']['template'])->save($this->forceDir($destination . '/' . $templates['optional']['autocomplete']['filename']));
			$this->incrementProgressBar();
		}

		// List of undocumented elements
		if ($undocumentedEnabled) {
			$label = function($element) {
				if ($element instanceof ApiReflection) {
					return 'Class';
				} elseif ($element instanceof ReflectionMethod) {
					return sprintf('Method %s()', $element->getName());
				} elseif ($element instanceof ReflectionConstant) {
					return sprintf('Constant %s', $element->getName());
				} elseif ($element instanceof ReflectionProperty) {
					return sprintf('Property $%s', $element->getName());
				} elseif ($element instanceof ReflectionParameter) {
					return sprintf('Parameter $%s', $element->getName());
				} else {
					return $element->getName();
				}
			};
			$normalize = function($string) {
				return preg_replace('~\s+~', ' ', $string);
			};

			$undocumented = array();
			foreach ($classTypes as $type) {
				foreach ($$type as $class) {
					// Check only "documented" classes (except internal - no documentation)
					if (!$class->isDocumented() || $class->isInternal()) {
						continue;
					}

					$tokens = $class->getBroker()->getFileTokens($class->getFileName());

					foreach (array_merge(array($class), array_values($class->getOwnMethods()), array_values($class->getOwnConstants()), array_values($class->getOwnProperties())) as $element) {
						$annotations = $element->getAnnotations();

						// Documentation
						if (empty($annotations)) {
							$undocumented[$class->getName()][] = sprintf('%s: Missing documentation.', $label($element));
							continue;
						}

						// Description
						if (!isset($annotations[ReflectionAnnotation::SHORT_DESCRIPTION])) {
							$undocumented[$class->getName()][] = sprintf('%s: Missing description.', $label($element));
						}

						// Documentation of method
						if ($element instanceof ReflectionMethod) {
							// Parameters
							foreach ($element->getParameters() as $no => $parameter) {
								if (!isset($annotations['param'][$no])) {
									$undocumented[$class->getName()][] = sprintf('%s: %s: Missing documentation.', $label($element), $label($parameter));
									continue;
								}

								if (!preg_match('~^[\w\\\\]+(?:\|[\w\\\\]+)*\s+\$' . $parameter->getName() . '(?:\s+.+)?$~s', $annotations['param'][$no])) {
									$undocumented[$class->getName()][] = sprintf('%s: %s: Invalid documentation "%s".', $label($element), $label($parameter), $normalize($annotations['param'][$no]));
								}

								unset($annotations['param'][$no]);
							}
							if (isset($annotations['param'])) {
								foreach ($annotations['param'] as $annotation) {
									$undocumented[$class->getName()][] = sprintf('%s: Existing documentation "%s" of nonexistent parameter.', $label($element), $normalize($annotation));
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
								$undocumented[$class->getName()][] = sprintf('%s: Missing documentation of return value.', $label($element));
							} elseif (isset($annotations['return'])) {
								if (!$return && 'void' !== $annotations['return'][0] && !$class->isInterface() && !$element->isAbstract()) {
									$undocumented[$class->getName()][] = sprintf('%s: Existing documentation "%s" of nonexistent return value.', $label($element), $normalize($annotations['return'][0]));
								} elseif (!preg_match('~^[\w\\\\]+(?:\|[\w\\\\]+)*(?:\s+.+)?$~s', $annotations['return'][0])) {
									$undocumented[$class->getName()][] = sprintf('%s: Invalid documentation "%s" of return value.', $label($element), $normalize($annotations['return'][0]));
								}
							}
							if (isset($annotations['return'][1])) {
								$undocumented[$class->getName()][] = sprintf('%s: Duplicate documentation "%s" of return value.', $label($element), $normalize($annotations['return'][1]));
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
								$undocumented[$class->getName()][] = sprintf('%s: Missing documentation of throwing an exception.', $label($element));
							} elseif (isset($annotations['throws'])	&& !preg_match('~^[\w\\\\]+(?:\|[\w\\\\]+)*(?:\s+.+)?$~s', $annotations['throws'][0])) {
								$undocumented[$class->getName()][] = sprintf('%s: Invalid documentation "%s" of throwing an exception.', $label($element), $normalize($annotations['throws'][0]));
							}
						}

						// Data type of constants & properties
						if ($element instanceof ReflectionProperty || $element instanceof ReflectionConstant) {
							if (!isset($annotations['var'])) {
								$undocumented[$class->getName()][] = sprintf('%s: Missing documentation of the data type.', $label($element));
							} elseif (!preg_match('~^[\w\\\\]+(?:\|[\w\\\\]+)*(?:\s+.+)?$~s', $annotations['var'][0])) {
								$undocumented[$class->getName()][] = sprintf('%s: Invalid documentation "%s" of the data type.', $label($element), $normalize($annotations['var'][0]));
							}

							if (isset($annotations['var'][1])) {
								$undocumented[$class->getName()][] = sprintf('%s: Duplicate documentation "%s" of the data type.', $label($element), $normalize($annotations['var'][1]));
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
			foreach ($undocumented as $className => $elements) {
				fwrite($fp, sprintf("%s\n%s\n", $className, str_repeat('-', strlen($className))));
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
		if ($deprecatedEnabled) {
			$deprecatedFilter = function($element) {return $element->isDeprecated();};
			$template->deprecatedClasses = array_filter($classes, $deprecatedFilter);
			$template->deprecatedInterfaces = array_filter($interfaces, $deprecatedFilter);
			$template->deprecatedExceptions = array_filter($exceptions, $deprecatedFilter);

			$template->deprecatedMethods = array();
			$template->deprecatedConstants = array();
			$template->deprecatedProperties = array();
			foreach ($classTypes as $type) {
				foreach ($$type as $class) {
					if ($class->isDeprecated()) {
						continue;
					}

					$template->deprecatedMethods += array_filter($class->getOwnMethods(), $deprecatedFilter);
					$template->deprecatedConstants += array_filter($class->getOwnConstants(), $deprecatedFilter);
					$template->deprecatedProperties += array_filter($class->getOwnProperties(), $deprecatedFilter);
				}
			}

			$template->setFile($templatePath . '/' . $templates['optional']['deprecated']['template'])->save($this->forceDir($destination . '/' . $templates['optional']['deprecated']['filename']));

			$this->incrementProgressBar();

			unset($deprecatedFilter);
			unset($template->deprecatedClasses);
			unset($template->deprecatedInterfaces);
			unset($template->deprecatedExceptions);
			unset($template->deprecatedMethods);
			unset($template->deprecatedConstants);
			unset($template->deprecatedProperties);
		}

		// List of tasks
		if ($todoEnabled) {
			$todoFilter = function($element) {return $element->hasAnnotation('todo');};
			$template->todoClasses = array_filter($classes, $todoFilter);
			$template->todoInterfaces = array_filter($interfaces, $todoFilter);
			$template->todoExceptions = array_filter($exceptions, $todoFilter);

			$template->todoMethods = array();
			$template->todoConstants = array();
			$template->todoProperties = array();
			foreach ($classTypes as $type) {
				foreach ($$type as $class) {
					$template->todoMethods += array_filter($class->getOwnMethods(), $todoFilter);
					$template->todoConstants += array_filter($class->getOwnConstants(), $todoFilter);
					$template->todoProperties += array_filter($class->getOwnProperties(), $todoFilter);
				}
			}

			$template->setFile($templatePath . '/' . $templates['optional']['todo']['template'])->save($this->forceDir($destination . '/' . $templates['optional']['todo']['filename']));

			$this->incrementProgressBar();

			unset($todoFilter);
			unset($template->todoClasses);
			unset($template->todoInterfaces);
			unset($template->todoExceptions);
			unset($template->todoMethods);
			unset($template->todoConstants);
			unset($template->todoProperties);
		}

		// Classes/interfaces/exceptions tree
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

		// Generate package summary
		$this->forceDir($destination . '/' . $templates['main']['package']['filename']);
		foreach ($packages as $packageName => $package) {
			$template->package = $packageName;
			$template->namespace = null;
			$template->classes = $package['classes'];
			$template->interfaces = $package['interfaces'];
			$template->exceptions = $package['exceptions'];
			$template->setFile($templatePath . '/' . $templates['main']['package']['template'])->save($destination . '/' . $template->getPackageLink($packageName));

			$this->incrementProgressBar();
		}
		unset($packages);

		// Generate namespace summary
		$this->forceDir($destination . '/' . $templates['main']['namespace']['filename']);
		foreach ($namespaces as $namespaceName => $namespace) {
			$template->package = 1 === count($namespace['packages']) ? reset($namespace['packages']) : null;
			$template->namespace = $namespaceName;
			$template->classes = $namespace['classes'];
			$template->interfaces = $namespace['interfaces'];
			$template->exceptions = $namespace['exceptions'];
			$template->setFile($templatePath . '/' . $templates['main']['namespace']['template'])->save($destination . '/' . $template->getNamespaceLink($namespaceName));

			$this->incrementProgressBar();
		}

		// Generate class & interface & exception files
		$fshl = new \fshlParser('HTML_UTF8', P_TAB_INDENT | P_LINE_COUNTER);
		$this->forceDir($destination . '/' . $templates['main']['class']['filename']);
		$this->forceDir($destination . '/' . $templates['main']['source']['filename']);
		foreach ($classTypes as $type) {
			foreach ($$type as $class) {
				$template->package = $class->isInternal() ? 'PHP' : $class->getPackageName() ?: 'None';
				$template->namespace = $namespace = $class->isInternal() ? 'PHP' : $class->getNamespaceName() ?: 'None';
				$template->classes = $namespaces[$namespace]['classes'];
				$template->interfaces = $namespaces[$namespace]['interfaces'];
				$template->exceptions = $namespaces[$namespace]['exceptions'];

				$template->tree = array_merge(array_reverse($class->getParentClasses()), array($class));

				$template->directSubClasses = $class->getDirectSubClasses();
				uksort($template->directSubClasses, 'strcasecmp');
				$template->indirectSubClasses = $class->getIndirectSubClasses();
				uksort($template->indirectSubClasses, 'strcasecmp');

				$template->directImplementers = $class->getDirectImplementers();
				uksort($template->directImplementers, 'strcasecmp');
				$template->indirectImplementers = $class->getIndirectImplementers();
				uksort($template->indirectImplementers, 'strcasecmp');

				$template->ownMethods = $class->getOwnMethods();
				$template->ownConstants = $class->getOwnConstants();
				$template->ownProperties = $class->getOwnProperties();

				if ($class->isTokenized()) {
					$template->fileName = null;
					$file = $class->getFileName();
					foreach ($this->config->source as $source) {
						if (0 === strpos($file, $source)) {
							$template->fileName = str_replace('\\', '/', substr($file, strlen($source) + 1));
							break;
						}
					}
					if (null === $template->fileName) {
						throw new Exception(sprintf('Could not determine class %s relative path', $class->getName()));
					}
				}

				$template->class = $class;
				$template->setFile($templatePath . '/' . $templates['main']['class']['template'])->save($destination . '/' . $template->getClassLink($class));

				$this->incrementProgressBar();

				// Generate source codes
				if ($this->config->sourceCode && $class->isTokenized()) {
					$source = file_get_contents($class->getFileName());
					$source = str_replace(array("\r\n", "\r"), "\n", $source);

					$template->source = $fshl->highlightString('PHP', $source);
					$template->setFile($templatePath . '/' . $templates['main']['source']['template'])->save($destination . '/' . $template->getSourceLink($class, false));

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
}

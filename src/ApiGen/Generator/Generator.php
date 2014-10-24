<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\Parser\CharsetConvertor;
use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionProperty;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;
use ApiGen\Tree;
use ApiGen\FileSystem;
use ApiGen\FileSystem\FileSystem as FS;
use ApiGen\Reflection;
use ArrayObject;
use Nette;
use RuntimeException;


/**
 * @method Generator setParsedClasses(object)
 * @method Generator setParsedConstants(object)
 * @method Generator setParsedFunctions(object)
 * @method Generator setConfig()
 * @method Generator onGenerateStart(int $steps)
 * @method Generator onGenerateProgress(int $size)
 */
class Generator extends Nette\Object
{
	/**
	 * @var array
	 */
	public $onGenerateStart = array();

	/**
	 * @var array
	 */
	public $onGenerateProgress = array();

	/**
	 * @var array
	 */
	private $config;

	/**
	 * @var ArrayObject
	 */
	private $parsedClasses;

	/**
	 * @var ArrayObject
	 */
	private $parsedConstants;

	/**
	 * @var ArrayObject
	 */
	private $parsedFunctions;

	/**
	 * @var array
	 */
	private $packages = array();

	/**
	 * @var array
	 */
	private $namespaces = array();

	/**
	 * @var array
	 */
	private $classes = array();

	/**
	 * @var array
	 */
	private $interfaces = array();

	/**
	 * @var array
	 */
	private $traits = array();

	/**
	 * @var array
	 */
	private $exceptions = array();

	/**
	 * @var array
	 */
	private $constants = array();

	/**
	 * @var array
	 */
	private $functions = array();

	/**
	 * @var CharsetConvertor
	 */
	private $charsetConvertor;

	/**
	 * @var SourceCodeHighlighter
	 */
	private $sourceCodeHighlighter;

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var RelativePathResolver
	 */
	private $relativePathResolver;

	/**
	 * @var FileSystem\Finder
	 */
	private $finder;

	/**
	 * @var ElementResolver
	 */
	private $elementResolver;

	/**
	 * @var FileSystem\Zip
	 */
	private $zip;


	public function __construct(CharsetConvertor $charsetConvertor, FileSystem\Zip $zip,
	                            SourceCodeHighlighter $sourceCodeHighlighter, TemplateFactory $templateFactory,
								RelativePathResolver $relativePathResolver, FileSystem\Finder $finder,
								ElementResolver $elementResolver)
	{
		$this->charsetConvertor = $charsetConvertor;
		$this->sourceCodeHighlighter = $sourceCodeHighlighter;
		$this->templateFactory = $templateFactory;
		$this->relativePathResolver = $relativePathResolver;
		$this->finder = $finder;
		$this->elementResolver = $elementResolver;
		$this->zip = $zip;

		$this->parsedClasses = new ArrayObject;
		$this->parsedConstants = new ArrayObject;
		$this->parsedFunctions = new ArrayObject;
	}


	/**
	 * Generates API documentation.
	 *
	 * @throws \RuntimeException If destination directory is not writable.
	 */
	public function generate()
	{
		// Copy resources
		foreach ($this->config['template']['resources'] as $resourceSource => $resourceDestination) {
			// File
			$resourcePath = $this->getTemplateDir() . DS . $resourceSource;
			if (is_file($resourcePath)) {
				copy($resourcePath, FS::forceDir($this->config['destination']  . DS . $resourceDestination));
				continue;
			}

			// Dir
			$iterator = Nette\Utils\Finder::findFiles('*')->from($resourcePath)->getIterator();
			foreach ($iterator as $item) {
				copy($item->getPathName(), FS::forceDir($this->config['destination']
					. DS . $resourceDestination
					. DS . $iterator->getSubPathName()));
			}
		}

		// Categorize by packages and namespaces
		$this->categorize();

		// Prepare progressbar & stuffs
		$steps = count($this->packages)
			+ count($this->namespaces)
			+ count($this->classes)
			+ count($this->interfaces)
			+ count($this->traits)
			+ count($this->exceptions)
			+ count($this->constants)
			+ count($this->functions)
			+ count($this->config['template']['templates']['common'])
			+ (int) $this->config['tree']
			+ (int) $this->config['deprecated']
			+ (int) $this->config['todo']
			+ (int) $this->config['download']
			+ (int) $this->isSitemapEnabled()
			+ (int) $this->isOpensearchEnabled()
			+ (int) $this->isRobotsEnabled();

		$tokenizedFilter = function (ReflectionClass $class) {
			return $class->isTokenized();
		};
		$steps += count(array_filter($this->classes, $tokenizedFilter))
			+ count(array_filter($this->interfaces, $tokenizedFilter))
			+ count(array_filter($this->traits, $tokenizedFilter))
			+ count(array_filter($this->exceptions, $tokenizedFilter))
			+ count($this->constants)
			+ count($this->functions);
		unset($tokenizedFilter);

		$this->onGenerateStart($steps);

		$tmp = $this->config['destination'] . DS . '_' . uniqid();

		FS::deleteDir($tmp);
		@mkdir($tmp, 0755, TRUE);

		// Common files
		$this->generateCommon();

		// Optional files
		$this->generateOptional();

		// List of deprecated elements
		if ($this->config['deprecated']) {
			$this->generateDeprecated();
		}

		// List of tasks
		if ($this->config['todo']) {
			$this->generateTodo();
		}

		// Classes/interfaces/traits/exceptions tree
		if ($this->config['tree']) {
			$this->generateTree();
		}

		// Generate packages summary
		$this->generatePackages();

		// Generate namespaces summary
		$this->generateNamespaces();

		// Generate classes, interfaces, traits, exceptions, constants and functions files
		$this->generateElements();

		// Generate ZIP archive
		if ($this->config['download']) {
			$this->zip->generate();
			$this->onGenerateProgress(1);
		}

		// Delete temporary directory
		FS::deleteDir($tmp);
	}


	/**
	 * Categorizes by packages and namespaces.
	 */
	private function categorize()
	{
		foreach (array('classes', 'constants', 'functions') as $type) {
			foreach ($this->{'parsed' . ucfirst($type)} as $elementName => $element) {
				/** @var ReflectionClass|ReflectionElement $element */
				if ( ! $element->isDocumented()) {
					continue;
				}

				$packageName = $element->getPseudoPackageName();
				$namespaceName = $element->getPseudoNamespaceName();

				if ($element instanceof ReflectionConstant) {
					/** @var $element ReflectionConstant */
					$this->constants[$elementName] = $element;
					$this->packages[$packageName]['constants'][$elementName] = $element;
					$this->namespaces[$namespaceName]['constants'][$element->getShortName()] = $element;

				} elseif ($element instanceof ReflectionFunction) {
					/** @var $element ReflectionFunction */
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

		$namespacesEnabled = ('auto' === $this->config['groups'] && ($userNamespacesCount > 0 || 0 === $userPackagesCount)) || 'namespaces' === $this->config['groups'];
		$packagesEnabled = ('auto' === $this->config['groups'] && ! $namespacesEnabled) || 'packages' === $this->config['groups'];

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
	}


	/**
	 * Sorts and filters groups.
	 *
	 * @return array
	 */
	private function sortGroups(array $groups)
	{
		// Don't generate only 'None' groups
		if (count($groups) === 1 && isset($groups['None'])) {
			return array();
		}

		$emptyList = array(
			'classes' => array(),
			'interfaces' => array(),
			'traits' => array(),
			'exceptions' => array(),
			'constants' => array(),
			'functions' => array()
		);

		$groupNames = array_keys($groups);
		$lowerGroupNames = array_flip(array_map(function ($y) {
			return strtolower($y);
		}, $groupNames));

		foreach ($groupNames as $groupName) {
			// Add missing parent groups
			$parent = '';
			foreach (explode('\\', $groupName) as $part) {
				$parent = ltrim($parent . '\\' . $part, '\\');
				if ( ! isset($lowerGroupNames[strtolower($parent)])) {
					$groups[$parent] = $emptyList;
				}
			}

			// Add missing element types
			foreach ($this->getElementTypes() as $type) {
				if ( ! isset($groups[$groupName][$type])) {
					$groups[$groupName][$type] = array();
				}
			}
		}

		$main = $this->config['main'];
		uksort($groups, function ($one, $two) use ($main) {
			// \ as separator has to be first
			$one = str_replace('\\', ' ', $one);
			$two = str_replace('\\', ' ', $two);

			if ($main) {
				if (strpos($one, $main) === 0 && strpos($two, $main) !== 0) {
					return -1;

				} elseif (strpos($one, $main) !== 0 && strpos($two, $main) === 0) {
					return 1;
				}
			}

			return strcasecmp($one, $two);
		});

		return $groups;
	}


	/**
	 * Generates common files.
	 */
	private function generateCommon()
	{
		$template = $this->templateFactory->create();
		$template = $this->addBaseVariablesToTemplate($template);

		// Elements for autocomplete
		$elements = array();
		$autocomplete = array_flip($this->config['autocomplete']);
		foreach ($this->getElementTypes() as $type) {
			foreach ($this->$type as $element) {
				if ($element instanceof ReflectionClass) {
					/** @var ReflectionClass $element */
					if (isset($autocomplete['classes'])) {
						$elements[] = array('c', $element->getPrettyName());
					}
					if (isset($autocomplete['methods'])) {
						foreach ($element->getOwnMethods() as $method) {
							$elements[] = array('m', $method->getPrettyName());
						}
						foreach ($element->getOwnMagicMethods() as $method) {
							$elements[] = array('mm', $method->getPrettyName());
						}
					}
					if (isset($autocomplete['properties'])) {
						foreach ($element->getOwnProperties() as $property) {
							$elements[] = array('p', $property->getPrettyName());
						}
						foreach ($element->getOwnMagicProperties() as $property) {
							$elements[] = array('mp', $property->getPrettyName());
						}
					}
					if (isset($autocomplete['classconstants'])) {
						foreach ($element->getOwnConstants() as $constant) {
							$elements[] = array('cc', $constant->getPrettyName());
						}
					}

				} elseif ($element instanceof ReflectionConstant && isset($autocomplete['constants'])) {
					$elements[] = array('co', $element->getPrettyName());

				} elseif ($element instanceof ReflectionFunction && isset($autocomplete['functions'])) {
					$elements[] = array('f', $element->getPrettyName());
				}
			}
		}
		usort($elements, function ($one, $two) {
			return strcasecmp($one[1], $two[1]);
		});
		$template->elements = $elements;

		foreach ($this->config['template']['templates']['common'] as $source => $destination) {
			$template->setFile($this->getTemplateDir() . DS . $source)
				->save($this->config['destination'] . DS . $destination);

			$this->onGenerateProgress(1);
		}

		unset($template->elements);
	}


	/**
	 * Generates optional files.
	 */
	private function generateOptional()
	{
		$template = $this->templateFactory->create();
		$template = $this->addBaseVariablesToTemplate($template);

		if ($this->isSitemapEnabled()) {
			$template->setFile($this->getTemplatePath('sitemap', 'optional'))
				->save($this->getTemplateFileName('sitemap', 'optional'));

			$this->onGenerateProgress(1);
		}

		if ($this->isOpensearchEnabled()) {
			$template->setFile($this->getTemplatePath('opensearch', 'optional'))
				->save($this->getTemplateFileName('opensearch', 'optional'));

			$this->onGenerateProgress(1);
		}

		if ($this->isRobotsEnabled()) {
			$template->setFile($this->getTemplatePath('robots', 'optional'))
				->save($this->getTemplateFileName('robots', 'optional'));

			$this->onGenerateProgress(1);
		}
	}


	/**
	 * Generates list of deprecated elements.
	 */
	private function generateDeprecated()
	{
		$template = $this->templateFactory->create();
		$template = $this->addBaseVariablesToTemplate($template);
		$this->prepareTemplate('deprecated');

		$deprecatedFilter = function ($element) {
			/** @var ReflectionElement $element */
			return $element->isDeprecated();
		};

		$template->deprecatedMethods = array();
		$template->deprecatedConstants = array();
		$template->deprecatedProperties = array();
		foreach (array_reverse($this->getElementTypes()) as $type) {
			$template->{'deprecated' . ucfirst($type)} = array_filter(array_filter($this->$type, $this->getMainFilter()), $deprecatedFilter);

			if ($type === 'constants' || $type === 'functions') {
				continue;
			}

			foreach ($this->$type as $class) {
				/** @var ReflectionClass $class */
				if ( ! $class->isMain()) {
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

		$template->setFile($this->getTemplatePath('deprecated'))
			->save($this->getTemplateFileName('deprecated'));

		foreach ($this->getElementTypes() as $type) {
			unset($template->{'deprecated' . ucfirst($type)});
		}
		unset($template->deprecatedMethods);
		unset($template->deprecatedProperties);

		$this->onGenerateProgress(1);
	}


	/**
	 * Generates list of tasks.
	 */
	private function generateTodo()
	{
		$template = $this->templateFactory->create();
		$template = $this->addBaseVariablesToTemplate($template);
		$this->prepareTemplate('todo');

		$todoFilter = function ($element) {
			/** @var ReflectionElement $element */
			return $element->hasAnnotation('todo');
		};

		$template->todoMethods = array();
		$template->todoConstants = array();
		$template->todoProperties = array();
		foreach (array_reverse($this->getElementTypes()) as $type) {
			$template->{'todo' . ucfirst($type)} = array_filter(array_filter($this->$type, $this->getMainFilter()), $todoFilter);

			if ($type === 'constants' || $type === 'functions') {
				continue;
			}

			foreach ($this->$type as $class) {
				/** @var ReflectionClass $class */
				if ( ! $class->isMain()) {
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

		$template->setFile($this->getTemplatePath('todo'))
			->save($this->getTemplateFileName('todo'));

		foreach ($this->getElementTypes() as $type) {
			unset($template->{'todo' . ucfirst($type)});
		}
		unset($template->todoMethods);
		unset($template->todoProperties);

		$this->onGenerateProgress(1);
	}


	/**
	 * @return Generator
	 */
	private function generateTree()
	{
		$template = $this->templateFactory->create();
		$template = $this->addBaseVariablesToTemplate($template);
		$this->prepareTemplate('tree');

		$classTree = array();
		$interfaceTree = array();
		$traitTree = array();
		$exceptionTree = array();

		$processed = array();
		foreach ($this->parsedClasses as $className => $reflection) {
			if ( ! $reflection->isMain() || ! $reflection->isDocumented() || isset($processed[$className])) {
				continue;
			}

			/** @var ReflectionClass $reflection */
			if ($reflection->getParentClassName() === NULL) {
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
					if ($level === 0) {
						// The topmost parent decides about the reflection type
						/** @var ReflectionClass $parent */
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

					if ( ! isset($t[$parentName])) {
						$t[$parentName] = array();
						$processed[$parentName] = TRUE;
						ksort($t, SORT_STRING);
					}

					$t = &$t[$parentName];
				}
			}
			$t[$className] = array();
			ksort($t, SORT_STRING);
			$processed[$className] = TRUE;
			unset($t);
		}


		$template->classTree = new Tree($classTree, $this->parsedClasses);
		$template->interfaceTree = new Tree($interfaceTree, $this->parsedClasses);
		$template->traitTree = new Tree($traitTree, $this->parsedClasses);
		$template->exceptionTree = new Tree($exceptionTree, $this->parsedClasses);

		$template->setFile($this->getTemplatePath('tree'))
			->save($this->getTemplateFileName('tree'));

		unset($template->classTree);
		unset($template->interfaceTree);
		unset($template->traitTree);
		unset($template->exceptionTree);

		$this->onGenerateProgress(1);
	}


	/**
	 * Generates packages summary.
	 */
	private function generatePackages()
	{
		if (empty($this->packages)) {
			return;
		}

		$template = $this->templateFactory->create();
		$template = $this->addBaseVariablesToTemplate($template);
		$this->prepareTemplate('package');

		$template->namespace = NULL;

		foreach ($this->packages as $packageName => $package) {
			$template->package = $packageName;
			$template->subpackages = array_filter($template->packages, function ($subpackageName) use ($packageName) {
				return (bool) preg_match('~^' . preg_quote($packageName) . '\\\\[^\\\\]+$~', $subpackageName);
			});
			$template->classes = $package['classes'];
			$template->interfaces = $package['interfaces'];
			$template->traits = $package['traits'];
			$template->exceptions = $package['exceptions'];
			$template->constants = $package['constants'];
			$template->functions = $package['functions'];
			$template->setFile($this->getTemplatePath('package'))
				->save($this->config['destination'] . DS . $template->packageUrl($packageName));

			$this->onGenerateProgress(1);
		}
		unset($template->subpackages);
	}


	/**
	 * Generates namespaces summary.
	 */
	private function generateNamespaces()
	{
		if (empty($this->namespaces)) {
			return;
		}

		$template = $this->templateFactory->create();
		$template = $this->addBaseVariablesToTemplate($template);
		$this->prepareTemplate('namespace');

		$template->package = NULL;

		foreach ($this->namespaces as $namespaceName => $namespace) {
			$template->namespace = $namespaceName;
			$template->subnamespaces = array_filter($template->namespaces, function ($subnamespaceName) use ($namespaceName) {
				return (bool) preg_match('~^' . preg_quote($namespaceName) . '\\\\[^\\\\]+$~', $subnamespaceName);
			});
			$template->classes = $namespace['classes'];
			$template->interfaces = $namespace['interfaces'];
			$template->traits = $namespace['traits'];
			$template->exceptions = $namespace['exceptions'];
			$template->constants = $namespace['constants'];
			$template->functions = $namespace['functions'];
			$template->setFile($this->getTemplatePath('namespace'))
				->save($this->config['destination'] . DS . $template->namespaceUrl($namespaceName));

			$this->onGenerateProgress(1);
		}
		unset($template->subnamespaces);
	}


	/**
	 * Generate classes, interfaces, traits, exceptions, constants and functions files.
	 */
	private function generateElements()
	{
		$template = $this->templateFactory->create();
		$template = $this->addBaseVariablesToTemplate($template);

		if ( ! empty($this->classes) || ! empty($this->interfaces) || ! empty($this->traits) || ! empty($this->exceptions)) {
			$this->prepareTemplate('class');
		}
		if ( ! empty($this->constants)) {
			$this->prepareTemplate('constant');
		}
		if ( ! empty($this->functions)) {
			$this->prepareTemplate('function');
		}
		$this->prepareTemplate('source');

		// Add @usedby annotation
		foreach ($this->getElementTypes() as $type) {
			foreach ($this->$type as $parentElement) {
				$elements = array($parentElement);
				if ($parentElement instanceof ReflectionClass) {
					$elements = array_merge(
						$elements,
						array_values($parentElement->getOwnMethods()),
						array_values($parentElement->getOwnConstants()),
						array_values($parentElement->getOwnProperties())
					);
				}
				/** @var ReflectionElement $element */
				foreach ($elements as $element) {
					$uses = $element->getAnnotation('uses');
					if ($uses === NULL) {
						continue;
					}
					foreach ($uses as $value) {
						list($link, $description) = preg_split('~\s+|$~', $value, 2);
						$resolved = $this->elementResolver->resolveElement($link, $element);
						if ($resolved !== NULL) {
							$resolved->addAnnotation('usedby', $element->getPrettyName() . ' ' . $description);
						}
					}
				}
			}
		}

		$template->package = NULL;
		$template->namespace = NULL;
		$template->classes = $this->classes;
		$template->interfaces = $this->interfaces;
		$template->traits = $this->traits;
		$template->exceptions = $this->exceptions;
		$template->constants = $this->constants;
		$template->functions = $this->functions;
		foreach ($this->getElementTypes() as $type) {
			foreach ($this->$type as $element) {
				if ( ! empty($this->namespaces)) {
					$template->namespace = $namespaceName = $element->getPseudoNamespaceName();
					$template->classes = $this->namespaces[$namespaceName]['classes'];
					$template->interfaces = $this->namespaces[$namespaceName]['interfaces'];
					$template->traits = $this->namespaces[$namespaceName]['traits'];
					$template->exceptions = $this->namespaces[$namespaceName]['exceptions'];
					$template->constants = $this->namespaces[$namespaceName]['constants'];
					$template->functions = $this->namespaces[$namespaceName]['functions'];

				} elseif ( ! empty($this->packages)) {
					$template->package = $packageName = $element->getPseudoPackageName();
					$template->classes = $this->packages[$packageName]['classes'];
					$template->interfaces = $this->packages[$packageName]['interfaces'];
					$template->traits = $this->packages[$packageName]['traits'];
					$template->exceptions = $this->packages[$packageName]['exceptions'];
					$template->constants = $this->packages[$packageName]['constants'];
					$template->functions = $this->packages[$packageName]['functions'];
				}

				$template->class = NULL;
				$template->constant = NULL;
				$template->function = NULL;
				if ($element instanceof ReflectionClass) {
					/** @var ReflectionClass $element */
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

					$template->class = $element;

					$template->setFile($this->getTemplatePath('class'))
						->save($this->config['destination'] . DS . $template->classUrl($element));

				} elseif ($element instanceof ReflectionConstant) {
					// Constant
					$template->constant = $element;

					$template->setFile($this->getTemplatePath('constant'))
						->save($this->config['destination'] . DS . $template->constantUrl($element));

				} elseif ($element instanceof ReflectionFunction) {
					// Function
					$template->function = $element;

					$template->setFile($this->getTemplatePath('function'))
						->save($this->config['destination'] . DS . $template->functionUrl($element));
				}

				$this->onGenerateProgress(1);

				// Generate source codes
				if ($element->isTokenized()) {
					$template->fileName = $this->relativePathResolver->getRelativePath($element->getFileName());
					$content = $this->charsetConvertor->convertFile($element->getFileName());
					$template->source = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);
					$template->setFile($this->getTemplatePath('source'))
						->save($this->config['destination'] . DS . $template->sourceUrl($element, FALSE));

					$this->onGenerateProgress(1);
				}
			}
		}
	}


	/**
	 * Checks if sitemap.xml is enabled.
	 *
	 * @return boolean
	 */
	private function isSitemapEnabled()
	{
		return ! empty($this->config['baseUrl']) && $this->templateExists('sitemap', 'optional');
	}


	/**
	 * Checks if opensearch.xml is enabled.
	 *
	 * @return boolean
	 */
	private function isOpensearchEnabled()
	{
		return ! empty($this->config['googleCseId']) && ! empty($this->config['baseUrl']) && $this->templateExists('opensearch', 'optional');
	}


	/**
	 * Checks if robots.txt is enabled.
	 *
	 * @return boolean
	 */
	private function isRobotsEnabled()
	{
		return (bool) $this->config['baseUrl'] && $this->templateExists('robots', 'optional');
	}


	/**
	 * Sorts methods by FQN.
	 *
	 * @return integer
	 */
	private function sortMethods(ReflectionMethod $one, ReflectionMethod $two)
	{
		return strcasecmp(
			$one->getDeclaringClassName() . '::' . $one->getName(),
			$two->getDeclaringClassName() . '::' . $two->getName()
		);
	}


	/**
	 * Sorts constants by FQN.
	 *
	 * @return integer
	 */
	private function sortConstants(ReflectionConstant $one, ReflectionConstant $two)
	{
		return strcasecmp((
			$one->getDeclaringClassName() ?: $one->getNamespaceName()) . '\\' . $one->getName(),
			($two->getDeclaringClassName() ?: $two->getNamespaceName()) . '\\' . $two->getName()
		);
	}


	/**
	 * Sorts functions by FQN.
	 *
	 * @return integer
	 */
	private function sortFunctions(ReflectionFunction $one, ReflectionFunction $two)
	{
		return strcasecmp(
			$one->getNamespaceName() . '\\' . $one->getName(),
			$two->getNamespaceName() . '\\' . $two->getName()
		);
	}


	/**
	 * Sorts functions by FQN.
	 *
	 * @return integer
	 */
	private function sortProperties(ReflectionProperty $one, ReflectionProperty $two)
	{
		return strcasecmp(
			$one->getDeclaringClassName() . '::' . $one->getName(),
			$two->getDeclaringClassName() . '::' . $two->getName()
		);
	}


	/**
	 * @return array
	 */
	private function getElementTypes()
	{
		return array('classes', 'interfaces', 'traits', 'exceptions', 'constants', 'functions');
	}


	/**
	 * @return \Closure
	 */
	private function getMainFilter()
	{
		return function ($element) {
			/** @var ReflectionElement $element */
			return $element->isMain();
		};
	}


	/**
	 * @return string
	 */
	private function getTemplateDir()
	{
		return dirname($this->config['templateConfig']);
	}


	/**
	 * @param string $name
	 * @param string $type
	 * @return string
	 */
	private function getTemplatePath($name, $type = 'main')
	{
		return $this->getTemplateDir() . DS . $this->config['template']['templates'][$type][$name]['template'];
	}


	/**
	 * @param string $name
	 * @param string $type
	 * @return string
	 */
	private function getTemplateFileName($name, $type = 'main')
	{
		return $this->config['destination'] . DS . $this->config['template']['templates'][$type][$name]['filename'];
	}


	/**
	 * @param string $name
	 * @param string $type
	 * @return string
	 */
	private function templateExists($name, $type = 'main')
	{
		return isset($this->config['template']['templates'][$type][$name]);
	}


	/**
	 * Checks if template exists and creates dir.
	 *
	 * @param string $name
	 * @throws \RuntimeException
	 */
	private function prepareTemplate($name)
	{
		if ( ! $this->templateExists($name)) {
			throw new RuntimeException("Template for $name is not set");
		}

		FS::forceDir($this->getTemplateFileName($name));
	}


	/**
	 * @param Template|\stdClass $template
	 * @return Template|\stdClass
	 */
	private function addBaseVariablesToTemplate(Template $template)
	{
		$template->namespace = NULL;
		$template->package = NULL;
		$template->class = NULL;
		$template->constant = NULL;
		$template->function = NULL;

		$template->gitVersions = $this->config['git']['versions'];

		$template->namespaces = array_keys($this->namespaces);
		$template->packages = array_keys($this->packages);
		$template->classes = array_filter($this->classes, $this->getMainFilter());
		$template->interfaces = array_filter($this->interfaces, $this->getMainFilter());
		$template->traits = array_filter($this->traits, $this->getMainFilter());
		$template->exceptions = array_filter($this->exceptions, $this->getMainFilter());
		$template->constants = array_filter($this->constants, $this->getMainFilter());
		$template->functions = array_filter($this->functions, $this->getMainFilter());
		$template->archive = basename($this->zip->getArchivePath());
		return $template;
	}

}

<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\Charset\CharsetConvertor;
use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Configuration\Theme\ThemeConfigOptions as TCO;
use ApiGen\FileSystem;
use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Generator\SourceCodeHighlighter\SourceCodeHighlighter;
use ApiGen\Parser\Elements\AutocompleteElements;
use ApiGen\Parser\Elements\ElementSorter;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Parser\Elements\GroupSorter;
use ApiGen\Reflection;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;
use ApiGen\Templating\TemplateNavigator;
use ApiGen\Theme\ThemeResources;
use ArrayObject;
use Nette;
use RuntimeException;


/**
 * @method Generator setParsedClasses(object)
 * @method Generator setParsedConstants(object)
 * @method Generator setParsedFunctions(object)
 * @method Generator onGenerateStart($steps)
 * @method Generator onGenerateProgress($size)
 * @method Generator setConfig(array $config)
 */
class Generator extends Nette\Object
{

	/**
	 * @var array
	 */
	public $onGenerateStart = [];

	/**
	 * @var array
	 */
	public $onGenerateProgress = [];

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
	private $packages = [];

	/**
	 * @var array
	 */
	private $namespaces = [];

	/**
	 * @var array
	 */
	private $classes = [];

	/**
	 * @var array
	 */
	private $interfaces = [];

	/**
	 * @var array
	 */
	private $traits = [];

	/**
	 * @var array
	 */
	private $exceptions = [];

	/**
	 * @var array
	 */
	private $constants = [];

	/**
	 * @var array
	 */
	private $functions = [];

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
	 * @var ElementResolver
	 */
	private $elementResolver;

	/**
	  * @var TemplateNavigator
	 */
	private $templateNavigator;

	/**
	 * @var ThemeResources
	 */
	private $themeResources;

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var GroupSorter
	 */
	private $groupSorter;

	/**
	 * @var ElementSorter
	 */
	private $elementSorter;

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;

	/**
	 * @var AutocompleteElements
	 */
	private $autocompleteElements;


	public function __construct(
		CharsetConvertor $charsetConvertor,
		SourceCodeHighlighter $sourceCodeHighlighter,
		TemplateFactory $templateFactory,
		RelativePathResolver $relativePathResolver,
		ElementResolver $elementResolver,
		TemplateNavigator $templateNavigator,
		ThemeResources $themeResources,
		Configuration $configuration,
		GroupSorter $groupSorter,
		ElementSorter $elementSorter,
		ElementStorage $elementStorage,
		AutocompleteElements $autocompleteElements
	) {
		$this->charsetConvertor = $charsetConvertor;
		$this->sourceCodeHighlighter = $sourceCodeHighlighter;
		$this->templateFactory = $templateFactory;
		$this->relativePathResolver = $relativePathResolver;
		$this->elementResolver = $elementResolver;

		$this->parsedClasses = new ArrayObject;
		$this->parsedConstants = new ArrayObject;
		$this->parsedFunctions = new ArrayObject;
		$this->templateNavigator = $templateNavigator;
		$this->themeResources = $themeResources;
		$this->configuration = $configuration;
		$this->groupSorter = $groupSorter;
		$this->elementSorter = $elementSorter;
		$this->elementStorage = $elementStorage;
		$this->autocompleteElements = $autocompleteElements;
	}


	/**
	 * Generates API documentation.
	 *
	 * @throws \RuntimeException If destination directory is not writable.
	 */
	public function generate()
	{
		$this->themeResources->copyToDestination($this->config[CO::DESTINATION]);

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
			+ count($this->functions);

		if ($this->config[CO::SOURCE_CODE]) {
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
		}

		$this->onGenerateStart($steps);

		// Set elements to storage
		$this->elementStorage->setNamespaces($this->namespaces);
		$this->elementStorage->setPackages($this->packages);
		$this->elementStorage->setClasses($this->classes);
		$this->elementStorage->setInterfaces($this->interfaces);
		$this->elementStorage->setTraits($this->traits);
		$this->elementStorage->setExceptions($this->exceptions);
		$this->elementStorage->setConstants($this->constants);
		$this->elementStorage->setFunctions($this->functions);

		// Common files
		$this->generateCommon();

		// Generate packages summary
		$this->generatePackages();

		// Generate namespaces summary
		$this->generateNamespaces();

		// Generate classes, interfaces, traits, exceptions, constants and functions files
		$this->generateElements();

	}


	/**
	 * Categorizes by packages and namespaces.
	 */
	private function categorize()
	{
		foreach (['classes', 'constants', 'functions'] as $type) {
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
		$userPackagesCount = count(array_diff(array_keys($this->packages), ['PHP', 'None']));
		$userNamespacesCount = count(array_diff(array_keys($this->namespaces), ['PHP', 'None']));

		$namespacesEnabled = ($this->config[CO::GROUPS] === 'auto'
			&& ($userNamespacesCount > 0 || $userPackagesCount === 0))
			|| $this->config[CO::GROUPS] === 'namespaces';

		$packagesEnabled = ($this->config[CO::GROUPS] === 'auto' && ! $namespacesEnabled)
			|| $this->config[CO::GROUPS] === 'packages';

		if ($namespacesEnabled) {
			$this->packages = [];
			$this->namespaces = $this->groupSorter->sort($this->namespaces);

		} elseif ($packagesEnabled) {
			$this->namespaces = [];
			$this->packages = $this->groupSorter->sort($this->packages);

		} else {
			$this->namespaces = [];
			$this->packages = [];
		}
	}


	private function generateCommon()
	{
		$template = $this->templateFactory->create();
		$template = $this->addBaseVariablesToTemplate($template);
		$template->elements = $this->autocompleteElements->getElements();

		// todo: wip
		$themeTemplates = $this->config[CO::TEMPLATE]['templates'];
		$commonTemplates = [
			$themeTemplates[TCO::OVERVIEW],
			$themeTemplates[TCO::COMBINED],
			$themeTemplates[TCO::ELEMENT_LIST],
			$themeTemplates[TCO::E404]
		];

		foreach ($commonTemplates as $templateInfo) {
			$template->setFile($templateInfo['template'])
				->save($this->config[CO::DESTINATION] . '/' . $templateInfo['filename']);
			$this->onGenerateProgress(1);
		}

		unset($template->elements);
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
			$template->setFile($this->templateNavigator->getTemplatePath(TCO::PACKAGE))
				->save($this->config[CO::DESTINATION] . '/' . $template->packageUrl($packageName));

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
			$template->setFile($this->templateNavigator->getTemplatePath(TCO::T_NAMESPACE))
				->save($this->config[CO::DESTINATION] . '/' . $template->namespaceUrl($namespaceName));

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

		// Add @usedby annotation
		foreach ($this->getElementTypes() as $type) {
			foreach ($this->$type as $parentElement) {
				$elements = [$parentElement];
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
					$template->tree = array_merge(array_reverse($element->getParentClasses()), [$element]);

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

					$template->setFile($this->templateNavigator->getTemplatePath(TCO::T_CLASS))
						->save($this->config[CO::DESTINATION] . '/' . $template->classUrl($element));

				} elseif ($element instanceof ReflectionConstant) {
					// Constant
					$template->constant = $element;

					$template->setFile($this->templateNavigator->getTemplatePath(TCO::T_CLASS))
						->save($this->config[CO::DESTINATION] . '/' . $template->constantUrl($element));

				} elseif ($element instanceof ReflectionFunction) {
					// Function
					$template->function = $element;

					$template->setFile($this->templateNavigator->getTemplatePath(TCO::T_FUNCTION))
						->save($this->config[CO::DESTINATION] . '/' . $template->functionUrl($element));
				}

				$this->onGenerateProgress(1);

				// Generate source codes
				if ($this->config[CO::SOURCE_CODE] && $element->isTokenized()) {
					$template->fileName = $this->relativePathResolver->getRelativePath($element->getFileName());
					$content = $this->charsetConvertor->convertFileToUtf($element->getFileName());
					$template->source = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);
					$template->setFile($this->templateNavigator->getTemplatePath(TCO::SOURCE))
						->save($this->config[CO::DESTINATION] . '/' . $template->sourceUrl($element, FALSE));

					$this->onGenerateProgress(1);
				}
			}
		}
	}


	/**
	 * @return string[]
	 */
	private function getElementTypes()
	{
		return ['classes', 'interfaces', 'traits', 'exceptions', 'constants', 'functions'];
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

		$template->namespaces = array_keys($this->namespaces);
		$template->packages = array_keys($this->packages);
		$template->classes = array_filter($this->classes, $this->getMainFilter());
		$template->interfaces = array_filter($this->interfaces, $this->getMainFilter());
		$template->traits = array_filter($this->traits, $this->getMainFilter());
		$template->exceptions = array_filter($this->exceptions, $this->getMainFilter());
		$template->constants = array_filter($this->constants, $this->getMainFilter());
		$template->functions = array_filter($this->functions, $this->getMainFilter());
		$template->archive = '#temp-error'; // solved in TemplateElementLoader
		return $template;
	}

}

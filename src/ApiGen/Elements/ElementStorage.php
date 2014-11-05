<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Elements;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Parser\ParserStorage;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionFunction;
use Nette;


/**
 * @method array getNamespaces()
 * @method array getPackages()
 */
class ElementStorage extends Nette\Object
{

	const NAMESPACES = 'namespaces';
	const PACKAGES = 'packages';

	/**
	 * @var array
	 */
	private $namespaces = array();

	/**
	 * @var array
	 */
	private $packages = array();

	/**
	 * @var bool
	 */
	private $areElementsCategorized = FALSE;

	/**
	 * @var ReflectionConstant[]
	 */
	private $constants = array();

	/**
	 * @var ReflectionFunction[]
	 */
	private $functions = array();

	/**
	 * @var ReflectionClass[]
	 */
	private $classes = array();

	/**
	 * @var ReflectionClass[]
	 */
	private $exceptions = array();

	/**
	 * @var ReflectionClass[]
	 */
	private $interfaces = array();

	/**
	 * @var ReflectionClass[]
	 */
	private $traits = array();

	/**
	 * @var ParserStorage
	 */
	private $parserStorage;

	/**
	 * @var Elements
	 */
	private $elements;

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var GroupSorter
	 */
	private $groupSorter;

	/**
	 * @var ElementResolver
	 */
	private $elementResolver;


	public function __construct(
		ParserStorage $parserStorage,
		Elements $elements,
		Configuration $configuration,
		GroupSorter $groupSorter,
		ElementResolver $elementResolver
	) {
		$this->parserStorage = $parserStorage;
		$this->elements = $elements;
		$this->configuration = $configuration;
		$this->groupSorter = $groupSorter;
		$this->elementResolver = $elementResolver;
	}


	public function getElements()
	{
		if ($this->areElementsCategorized === FALSE) {
			$this->categorizeParsedElements();
		}

		$elements = array(
//			self::NAMESPACES => $this->namespaces,
//			self::PACKAGES => $this->packages,
			Elements::CLASSES => $this->classes,
			Elements::CONSTANTS => $this->constants,
			Elements::FUNCTIONS => $this->functions,
			Elements::INTERFACES => $this->interfaces,
			Elements::TRAITS => $this->traits,
			Elements::EXCEPTIONS => $this->exceptions
		);

		return $elements;
	}


	/**
	 * @param string $type
	 * @return array
	 * @throws \Exception
	 */
	public function getElementsByType($type)
	{
		$elements = $this->getElements();
		if (isset($elements[$type])) {
			return $elements[$type];
		}

		throw new \Exception('Elements of type ' . $type . ' not found');
	}


	/**
	 * @return int
	 */
	public function getElementCount()
	{
		$elements = $this->getElements();
		$counter = 0;
		foreach ($elements as $type => $elementList) {
			$counter += count($elementList);
		}
		return $counter;
	}


	/**
	 * @return bool
	 */
	public function hasNamespaces()
	{
		if (count($this->getNamespaces())) {
			return TRUE;
		}
		return FALSE;
	}


	private function categorizeParsedElements()
	{
		foreach ($this->parserStorage->getTypes() as $type) {
			$elements = $this->parserStorage->getElementsByType($type);
			foreach ($elements as $elementName => $element) {
				if ( ! $element->isDocumented()) {
					continue;
				}

				if ($element instanceof ReflectionConstant) {
					$elementType = Elements::CONSTANTS;
					$this->constants[$elementName] = $element;

				} elseif ($element instanceof ReflectionFunction) {
					$elementType = Elements::FUNCTIONS;
					$this->functions[$elementName] = $element;

				} elseif ($element->isInterface()) {
					$elementType = Elements::INTERFACES;
					$this->interfaces[$elementName] = $element;

				} elseif ($element->isTrait()) {
					$elementType = Elements::TRAITS;
					$this->traits[$elementName] = $element;

				} elseif ($element->isException()) {
					$elementType = Elements::INTERFACES;
					$this->exceptions[$elementName] = $element;

				} else {
					$elementType = Elements::CLASSES;
					$this->classes[$elementName] = $element;
				}

				$this->categorizeElementToNamespaceAndPackage($elementName, $elementType, $element);
			}

			$this->areElementsCategorized = TRUE;
		}

		$this->sortNamespacesAndPackages();
		$this->addUsedByAnnotation();
	}


	/**
	 * @param string $elementName
	 * @param string $elementType
	 * @param ReflectionElement|ReflectionClass $element
	 */
	private function categorizeElementToNamespaceAndPackage($elementName, $elementType, $element)
	{
		$packageName = $element->getPseudoPackageName();
		$namespaceName = $element->getPseudoNamespaceName();

		if ( ! isset($this->packages[$packageName])) {
			$this->packages[$packageName] = $this->elements->getEmptyList();
		}
		if ( ! isset($this->namespaces[$namespaceName])) {
			$this->namespaces[$namespaceName] = $this->elements->getEmptyList();
		}

		$this->packages[$packageName][$elementType][$elementName] = $element;
		$this->namespaces[$namespaceName][$elementType][$element->getShortName()] = $element;
	}


	private function sortNamespacesAndPackages()
	{
		$areNamespacesEnabled = $this->configuration->areNamespacesEnabled(
			$this->getNamespaceCount(),
			$this->getPackageCount()
		);
		$arePackagesEnabled = $this->configuration->arePackagesEnabled($areNamespacesEnabled);

		if ($areNamespacesEnabled) {
			$this->namespaces = $this->groupSorter->sort($this->namespaces);
			$this->packages = array();

		} elseif ($arePackagesEnabled) {
			$this->namespaces = array();
			$this->packages = $this->groupSorter->sort($this->packages);

		} else {
			$this->namespaces = array();
			$this->packages = array();
		}
	}


	/**
	 * @return int
	 */
	private function getNamespaceCount()
	{
		$nonDefaultNamespaces = array_diff(array_keys($this->namespaces), array('PHP', 'None'));
		return count($nonDefaultNamespaces);
	}


	/**
	 * @return int
	 */
	private function getPackageCount()
	{
		$nonDefaultPackages = array_diff(array_keys($this->packages), array('PHP', 'None'));
		return count($nonDefaultPackages);
	}


	private function addUsedByAnnotation()
	{
		foreach ($this->getElements() as $elementList) {
			foreach ($elementList as $parentElement) {
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
	}

}

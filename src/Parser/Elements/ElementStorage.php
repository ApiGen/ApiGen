<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Elements\GroupSorterInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;

class ElementStorage implements ElementStorageInterface
{

    /**
     * @var array
     */
    private $namespaces = [];

    /**
     * @var array
     */
    private $packages = [];

    /**
     * @var ClassReflectionInterface[]
     */
    private $classes = [];

    /**
     * @var ClassReflectionInterface[]
     */
    private $interfaces = [];

    /**
     * @var ClassReflectionInterface[]
     */
    private $traits = [];

    /**
     * @var ClassReflectionInterface[]
     */
    private $exceptions = [];

    /**
     * @var ConstantReflectionInterface[]
     */
    private $constants = [];

    /**
     * @var FunctionReflectionInterface[]
     */
    private $functions = [];

    /**
     * @var bool
     */
    private $areElementsCategorized = false;

    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var GroupSorterInterface
     */
    private $groupSorter;

    /**
     * @var ElementResolverInterface
     */
    private $elementResolver;


    public function __construct(
        ParserStorageInterface $parserResult,
        ConfigurationInterface $configuration,
        GroupSorterInterface $groupSorter,
        ElementResolverInterface $elementResolver
    ) {
        $this->parserStorage = $parserResult;
        $this->configuration = $configuration;
        $this->groupSorter = $groupSorter;
        $this->elementResolver = $elementResolver;
    }


    /**
     * {@inheritdoc}
     */
    public function getNamespaces()
    {
        $this->ensureCategorization();
        return $this->namespaces;
    }


    /**
     * {@inheritdoc}
     */
    public function getPackages()
    {
        $this->ensureCategorization();
        return $this->packages;
    }


    /**
     * {@inheritdoc}
     */
    public function getClasses()
    {
        $this->ensureCategorization();
        return $this->classes;
    }


    /**
     * {@inheritdoc}
     */
    public function getInterfaces()
    {
        $this->ensureCategorization();
        return $this->interfaces;
    }


    /**
     * {@inheritdoc}
     */
    public function getTraits()
    {
        $this->ensureCategorization();
        return $this->traits;
    }


    /**
     * {@inheritdoc}
     */
    public function getExceptions()
    {
        $this->ensureCategorization();
        return $this->exceptions;
    }


    /**
     * {@inheritdoc}
     */
    public function getConstants()
    {
        $this->ensureCategorization();
        return $this->constants;
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
         $this->ensureCategorization();
         return $this->functions;
    }


    /**
     * {@inheritdoc}
     */
    public function getClassElements()
    {
        return array_merge($this->getClasses(), $this->getTraits(), $this->getInterfaces(), $this->getExceptions());
    }


    /**
     * {@inheritdoc}
     */
    public function getElements()
    {
        $this->ensureCategorization();

        $elements = [
            Elements::CLASSES => $this->classes,
            Elements::CONSTANTS => $this->constants,
            Elements::FUNCTIONS => $this->functions,
            Elements::INTERFACES => $this->interfaces,
            Elements::TRAITS => $this->traits,
            Elements::EXCEPTIONS => $this->exceptions
        ];
        return $elements;
    }


    private function categorizeParsedElements()
    {
        foreach ($this->parserStorage->getTypes() as $type) {
            $elements = $this->parserStorage->getElementsByType($type);
            foreach ($elements as $elementName => $element) {
                if (! $element->isDocumented()) {
                    continue;
                }
                if ($element instanceof ConstantReflectionInterface) {
                    $elementType = Elements::CONSTANTS;
                    $this->constants[$elementName] = $element;

                } elseif ($element instanceof FunctionReflectionInterface) {
                    $elementType = Elements::FUNCTIONS;
                    $this->functions[$elementName] = $element;

                } elseif ($element->isInterface()) {
                    $elementType = Elements::INTERFACES;
                    $this->interfaces[$elementName] = $element;

                } elseif ($element->isTrait()) {
                    $elementType = Elements::TRAITS;
                    $this->traits[$elementName] = $element;

                } elseif ($element->isException()) {
                    $elementType = Elements::EXCEPTIONS;
                    $this->exceptions[$elementName] = $element;

                } else {
                    $elementType = Elements::CLASSES;
                    $this->classes[$elementName] = $element;
                }
                $this->categorizeElementToNamespaceAndPackage($elementName, $elementType, $element);
            }
        }
        $this->sortNamespacesAndPackages();
        $this->areElementsCategorized = true;
        $this->addUsedByAnnotation();
    }


    /**
     * @param string $elementName
     * @param string $elementType
     * @param ElementReflectionInterface $element
     */
    private function categorizeElementToNamespaceAndPackage(
        $elementName,
        $elementType,
        ElementReflectionInterface $element
    ) {
        $packageName = $element->getPseudoPackageName();
        $this->packages[$packageName][$elementType][$elementName] = $element;

        $namespaceName = $element->getPseudoNamespaceName();
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
            $this->packages = [];

        } elseif ($arePackagesEnabled) {
            $this->namespaces = [];
            $this->packages = $this->groupSorter->sort($this->packages);

        } else {
            $this->namespaces = [];
            $this->packages = [];
        }
    }


    /**
     * @return int
     */
    private function getNamespaceCount()
    {
        $nonDefaultNamespaces = array_diff(array_keys($this->namespaces), ['PHP', 'None']);
        return count($nonDefaultNamespaces);
    }


    /**
     * @return int
     */
    private function getPackageCount()
    {
        $nonDefaultPackages = array_diff(array_keys($this->packages), ['PHP', 'None']);
        return count($nonDefaultPackages);
    }


    private function addUsedByAnnotation()
    {
        foreach ($this->getElements() as $elementList) {
            foreach ($elementList as $parentElement) {
                $elements = $this->getSubElements($parentElement);

                /** @var ElementReflectionInterface $element */
                foreach ($elements as $element) {
                    $this->loadUsesToReferencedElementUsedby($element);
                }
            }
        }
    }


    private function ensureCategorization()
    {
        if ($this->areElementsCategorized === false) {
            $this->categorizeParsedElements();
        }
    }


    /**
     * @return array
     */
    private function getSubElements(ElementReflectionInterface $parentElement)
    {
        $elements = [$parentElement];
        if ($parentElement instanceof ClassReflectionInterface) {
            $elements = array_merge(
                $elements,
                array_values($parentElement->getOwnMethods()),
                array_values($parentElement->getOwnConstants()),
                array_values($parentElement->getOwnProperties())
            );
        }
        return $elements;
    }


    private function loadUsesToReferencedElementUsedby(ElementReflectionInterface $element)
    {
        $uses = $element->getAnnotation('uses');
        if ($uses === null) {
            return;
        }

        foreach ($uses as $value) {
            list($link, $description) = preg_split('~\s+|$~', $value, 2);
            $resolved = $this->elementResolver->resolveElement($link, $element);
            if ($resolved) {
                $resolved->addAnnotation('usedby', $element->getPrettyName() . ' ' . $description);
            }
        }
    }
}

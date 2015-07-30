<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Generator\TemplateGenerators\ConditionalTemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Tree;

class TreeGenerator implements ConditionalTemplateGeneratorInterface
{

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var TemplateFactoryInterface
     */
    private $templateFactory;

    /**
     * @var array
     */
    private $processed = [];

    /**
     * @var array[]
     */
    private $treeStorage = [
        ElementsInterface::CLASSES => [],
        ElementsInterface::INTERFACES => [],
        ElementsInterface::TRAITS => [],
        ElementsInterface::EXCEPTIONS => []
    ];

    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;


    public function __construct(
        Configuration $configuration,
        TemplateFactoryInterface $templateFactory,
        ParserStorageInterface $parserStorage
    ) {
        $this->configuration = $configuration;
        $this->templateFactory = $templateFactory;
        $this->parserStorage = $parserStorage;
    }


    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $template = $this->templateFactory->createForType('tree');

        $classes = $this->parserStorage->getClasses();
        foreach ($classes as $className => $reflection) {
            if ($this->canBeProcessed($reflection)) {
                $this->addToTreeByReflection($reflection);
            }
        }

        $this->sortTreeStorageElements();

        $template->setParameters([
            'classTree' => new Tree($this->treeStorage[ElementsInterface::CLASSES], $classes),
            'interfaceTree' => new Tree($this->treeStorage[ElementsInterface::INTERFACES], $classes),
            'traitTree' => new Tree($this->treeStorage[ElementsInterface::TRAITS], $classes),
            'exceptionTree' => new Tree($this->treeStorage[ElementsInterface::EXCEPTIONS], $classes)
        ]);

        $template->save();
    }


    /**
     * {@inheritdoc}
     */
    public function isAllowed()
    {
        return $this->configuration->getOption(CO::TREE);
    }


    /**
     * @return bool
     */
    private function canBeProcessed(ClassReflectionInterface $reflection)
    {
        if (! $reflection->isMain()) {
            return false;
        }
        if (! $reflection->isDocumented()) {
            return false;
        }
        if (isset($this->processed[$reflection->getName()])) {
            return false;
        }
        return true;
    }


    private function addToTreeByReflection(ClassReflectionInterface $reflection)
    {
        if ($reflection->getParentClassName() === null) {
            $type = $this->getTypeByReflection($reflection);
            $this->addToTreeByTypeAndName($type, $reflection->getName());

        } else {
            foreach (array_values(array_reverse($reflection->getParentClasses())) as $level => $parent) {
                $type = null;
                if ($level === 0) {
                    // The topmost parent decides about the reflection type
                    $type = $this->getTypeByReflection($reflection);
                }

                /** @var ReflectionClass $parent */
                $parentName = $parent->getName();
                if (! isset($this->treeStorage[$type][$parentName])) {
                    $this->addToTreeByTypeAndName($type, $parentName);
                }
            }
        }
    }


    /**
     * @return string
     */
    private function getTypeByReflection(ClassReflectionInterface $reflection)
    {
        if ($reflection->isInterface()) {
            return ElementsInterface::INTERFACES;

        } elseif ($reflection->isTrait()) {
            return ElementsInterface::TRAITS;

        } elseif ($reflection->isException()) {
            return ElementsInterface::EXCEPTIONS;

        } else {
            return ElementsInterface::CLASSES;
        }
    }


    /**
     * @param string $type
     * @param string $name
     */
    private function addToTreeByTypeAndName($type, $name)
    {
        $this->treeStorage[$type][$name] = [];
        $this->processed[$name] = true;
    }


    private function sortTreeStorageElements()
    {
        foreach ($this->treeStorage as $key => $elements) {
            ksort($elements, SORT_STRING);
            $this->treeStorage[$key] = $elements;
        }
    }
}

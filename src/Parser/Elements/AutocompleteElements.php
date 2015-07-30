<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\AutocompleteElementsInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Parser\Reflection\ReflectionBase;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionConstant;
use ApiGen\Parser\Reflection\ReflectionFunction;

class AutocompleteElements implements AutocompleteElementsInterface
{

    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    /**
     * @var array
     */
    private $elements = [];


    public function __construct(ElementStorageInterface $elementStorage)
    {
        $this->elementStorage = $elementStorage;
    }


    /**
     * {@inheritdoc}
     */
    public function getElements()
    {
        foreach ($this->elementStorage->getElements() as $type => $elementList) {
            foreach ($elementList as $element) {
                $this->processElement($element);
            }
        }

        $this->sortElements();

        return $this->elements;
    }


    private function processElement(ElementReflectionInterface $element)
    {
        if ($element instanceof ConstantReflectionInterface) {
            $this->elements[] = ['co', $element->getPrettyName()];

        } elseif ($element instanceof FunctionReflectionInterface) {
            $this->elements[] = ['f', $element->getPrettyName()];

        } elseif ($element instanceof ClassReflectionInterface) {
            $this->elements[] = ['c', $element->getPrettyName()];
        }
    }


    private function sortElements()
    {
        usort($this->elements, function ($one, $two) {
            return strcasecmp($one[1], $two[1]);
        });
    }
}

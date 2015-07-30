<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;

class SourceFilters extends Filters
{

    /**
     * @var ConfigurationInterface
     */
    private $configuration;


    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * @param string $name
     * @return string
     */
    public function staticFile($name)
    {
        $filename = $this->configuration->getOption('destination') . '/' . $name;
        if (is_file($filename)) {
            $name .= '?' . sha1_file($filename);
        }
        return $name;
    }


    /**
     * @param ElementReflectionInterface $element
     * @param bool $withLine Include file line number into the link
     * @return string
     */
    public function sourceUrl(ElementReflectionInterface $element, $withLine = true)
    {
        $file = '';
        if ($this->isDirectUrl($element)) {
            $elementName = $element->getName();
            if ($element instanceof ClassReflectionInterface) {
                $file = 'class-';

            } elseif ($element instanceof ConstantReflectionInterface) {
                $file = 'constant-';

            } elseif ($element instanceof FunctionReflectionInterface) {
                $file = 'function-';
            }

        } else {
            $elementName = $element->getDeclaringClassName();
            $file = 'class-';
        }

        $file .= $this->urlize($elementName);

        $url = sprintf($this->configuration->getOption('template')['templates']['source']['filename'], $file);
        if ($withLine) {
            $url .= $this->getElementLinesAnchor($element);
        }
        return $url;
    }


    /**
     * @return bool
     */
    private function isDirectUrl(ElementReflectionInterface $element)
    {
        if ($element instanceof ClassReflectionInterface
            || $element instanceof FunctionReflectionInterface
            || $element instanceof ConstantReflectionInterface
        ) {
            return true;
        }
        return false;
    }


    /**
     * @param LinedInterface $element
     * @return string
     */
    private function getElementLinesAnchor(LinedInterface $element)
    {
        $anchor = '#' . $element->getStartLine();
        if ($element->getStartLine() !== $element->getEndLine()) {
            $anchor .= '-' . $element->getEndLine();
        }
        return $anchor;
    }
}

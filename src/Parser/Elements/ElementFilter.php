<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementFilterInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;

class ElementFilter implements ElementFilterInterface
{

    /**
     * {@inheritdoc}
     */
    public function filterForMain(array $elements)
    {
        return array_filter($elements, function (ElementReflectionInterface $element) {
            return $element->isMain();
        });
    }


    /**
     * {@inheritdoc}
     */
    public function filterByAnnotation(array $elements, $annotation)
    {
        return array_filter($elements, function (ElementReflectionInterface $element) use ($annotation) {
            return $element->hasAnnotation($annotation);
        });
    }
}

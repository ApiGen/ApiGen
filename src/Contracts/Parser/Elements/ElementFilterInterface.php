<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Elements;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;

interface ElementFilterInterface
{

    /**
     * @param ElementReflectionInterface[] $elements
     * @return ElementReflectionInterface[]
     */
    public function filterForMain(array $elements);


    /**
     * @param ElementReflectionInterface[] $elements
     * @param string $annotation
     * @return ElementReflectionInterface[]
     */
    public function filterByAnnotation(array $elements, $annotation);
}

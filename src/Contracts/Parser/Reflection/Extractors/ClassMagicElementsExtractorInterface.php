<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;

interface ClassMagicElementsExtractorInterface
{

    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function getMagicProperties();


    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function getOwnMagicProperties();


    /**
     * @return array {[ declaringClassName => MagicMethodReflectionInterface[] ]}
     */
    public function getInheritedMagicProperties();


    /**
     * @return array {[ declaringClassName => MagicMethodReflectionInterface[] ]}
     */
    public function getUsedMagicProperties();


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getMagicMethods();


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getOwnMagicMethods();


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getUsedMagicMethods();
}

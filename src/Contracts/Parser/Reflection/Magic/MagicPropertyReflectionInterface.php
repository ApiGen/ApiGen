<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection\Magic;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;

interface MagicPropertyReflectionInterface extends PropertyReflectionInterface
{

    /**
     * @return bool
     */
    public function isDocumented();


    /**
     * @return string
     */
    public function getShortDescription();


    /**
     * @return string
     */
    public function getLongDescription();


    /**
     * @return string
     */
    public function getDocComment();


    /**
     * @return bool
     */
    public function isDeprecated();


    /**
     * @return self
     */
    public function setDeclaringClass(ClassReflectionInterface $declaringClass);


    /**
     * @return bool
     */
    public function isPrivate();


    /**
     * @return bool
     */
    public function isProtected();


    /**
     * @return bool
     */
    public function isPublic();


    /**
     * @return string
     */
    public function getFileName();


    /**
     * @return bool
     */
    public function isTokenized();
}

<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;
use TokenReflection\Exception\BaseException;

interface ElementReflectionInterface extends NamedInterface
{

    /**
     * @return bool
     */
    public function isMain();


    /**
     * @return bool
     */
    public function isValid();


    /**
     * @return bool
     */
    public function isDocumented();


    /**
     * @return bool
     */
    public function isDeprecated();


    /**
     * @return bool
     */
    public function inPackage();


    /**
     * @return string
     */
    public function getPackageName();


    /**
     * Returns element package name (including subpackage name).
     * For internal elements returns "PHP", for elements in global space returns "None".
     *
     * @return string
     */
    public function getPseudoPackageName();


    /**
     * @return bool
     */
    public function inNamespace();


    /**
     * @return string
     */
    public function getNamespaceName();


    /**
     * Returns element namespace name.
     * For internal elements returns "PHP", for elements in global space returns "None".
     *
     * @return string
     */
    public function getPseudoNamespaceName();


    /**
     * @return string[]
     */
    public function getNamespaceAliases();


    /**
     * Returns reflection element annotations.
     * Removes the short and long description.
     * In case of classes, functions and constants, @package, @subpackage, @author and @license annotations
     * are added from declaring files if not already present.
     *
     * @return array
     */
    public function getAnnotations();


    /**
     * @param string $name
     * @return array
     */
    public function getAnnotation($name);


    /**
     * @param string $name
     * @return bool
     */
    public function hasAnnotation($name);


    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function addAnnotation($name, $value);


    /**
     * @return string
     */
    public function getShortDescription();


    /**
     * @return string
     */
    public function getLongDescription();


    /**
     * @return string|bool
     */
    public function getDocComment();


    /**
     * @return string
     */
    public function getPrettyName();


    /**
     * Returns the unqualified name (UQN).
     *
     * @return string
     */
    public function getShortName();


    /**
     * @return int
     */
    public function getStartPosition();


    /**
     * @return int
     */
    public function getEndPosition();


    /**
     * @return self
     */
    public function addReason(BaseException $reason);


    /**
     * @return BaseException[]
     */
    public function getReasons();


    /**
     * @return bool
     */
    public function hasReasons();
}

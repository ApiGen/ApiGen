<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ExtensionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionConstant;
use ApiGen\Parser\Reflection\ReflectionElement;
use ApiGen\Parser\Reflection\ReflectionFunction;
use ApiGen\Parser\Reflection\ReflectionMethod;
use ApiGen\Parser\Reflection\ReflectionProperty;

/**
 * Builds links to a element documentation at php.net
 */
class PhpManualFilters extends Filters
{

    const PHP_MANUAL_URL = 'http://php.net/manual/en';

    /**
     * @var array [ className => callback ]
     */
    private $assignments = [];


    public function __construct()
    {
        $this->prepareAssignments();
    }


    /**
     * @param ElementReflectionInterface|ExtensionReflectionInterface|MethodReflectionInterface $element
     * @return string
     */
    public function manualUrl($element)
    {
        if ($element instanceof ExtensionReflectionInterface) {
            return $this->createExtensionUrl($element);
        }

        $class = $this->detectClass($element);
        if ($class && $this->isReservedClass($class)) {
            return self::PHP_MANUAL_URL . '/reserved.classes.php';
        }

        $className = get_class($element);
        if (isset($this->assignments[$className])) {
            return $this->assignments[$className]($element, $class);
        }
        return '';
    }


    /**
     * @return string
     */
    private function createExtensionUrl(ExtensionReflectionInterface $extensionReflection)
    {
        $extensionName = strtolower($extensionReflection->getName());
        if ($extensionName === 'core') {
            return self::PHP_MANUAL_URL;
        }

        if ($extensionName === 'date') {
            $extensionName = 'datetime';
        }

        return self::PHP_MANUAL_URL . '/book.' . $extensionName . '.php';
    }


    /**
     * @return array
     */
    private function prepareAssignments()
    {
        $this->assignments[ReflectionClass::class] = function ($element, $class) {
            return $this->createClassUrl($class);
        };
        $this->assignments[ReflectionMethod::class] = function ($element, $class) {
            return $this->createMethodUrl($element, $class);
        };
        $this->assignments[ReflectionFunction::class] = function ($element, $class) {
            return $this->createFunctionUrl($element);
        };
        $this->assignments[ReflectionProperty::class] = function ($element, $class) {
            return $this->createPropertyUrl($element, $class);
        };
        $this->assignments[ReflectionConstant::class] = function ($element, $class) {
            return $this->createConstantUrl($element, $class);
        };
    }


    /**
     * @return string
     */
    private function createClassUrl(ClassReflectionInterface $classReflection)
    {
        return self::PHP_MANUAL_URL . '/class.' . strtolower($classReflection->getName()) . '.php';
    }


    /**
     * @return string
     */
    private function createConstantUrl(
        ConstantReflectionInterface $reflectionConstant,
        ClassReflectionInterface $classReflection
    ) {
        return $this->createClassUrl($classReflection) . '#' . strtolower($classReflection->getName()) .
            '.constants.' . $this->getElementName($reflectionConstant);
    }


    /**
     * @return string
     */
    private function createPropertyUrl(
        PropertyReflectionInterface $reflectionProperty,
        ClassReflectionInterface $classReflection
    ) {
        return $this->createClassUrl($classReflection) . '#' . strtolower($classReflection->getName()) .
            '.props.' . $this->getElementName($reflectionProperty);
    }


    /**
     * @return string
     */
    private function createMethodUrl(
        MethodReflectionInterface $reflectionMethod,
        ClassReflectionInterface $reflectionClass
    ) {
        return self::PHP_MANUAL_URL . '/' . strtolower($reflectionClass->getName()) . '.' .
            $this->getElementName($reflectionMethod) . '.php';
    }


    /**
     * @return string
     */
    private function createFunctionUrl(
        ElementReflectionInterface $reflectionElement
    ) {
        return self::PHP_MANUAL_URL . '/function.' . strtolower($reflectionElement->getName()) . '.php';
    }


    /**
     * @return bool
     */
    private function isReservedClass(
        ClassReflectionInterface $class
    ) {
        $reservedClasses = ['stdClass', 'Closure', 'Directory'];
        return (in_array($class->getName(), $reservedClasses));
    }


    /**
     * @return string
     */
    private function getElementName(
        ElementReflectionInterface $element
    ) {
        return strtolower(strtr(ltrim($element->getName(), '_'), '_', '-'));
    }


    /**
     * @param ReflectionElement|string $element
     * @return string
     */
    private function detectClass($element)
    {
        if ($element instanceof ClassReflectionInterface) {
            return $element;
        }

        if ($element instanceof InClassInterface) {
            return $element->getDeclaringClass();
        }

        return '';
    }
}

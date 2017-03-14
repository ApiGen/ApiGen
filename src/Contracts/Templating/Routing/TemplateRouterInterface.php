<?php

namespace ApiGen\Contracts\Templating\Routing;

use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;

interface TemplateRouterInterface
{

    /**
     * @param string $name
     * @return string
     */
    public function getNamespaceRoute($name);


    /**
     * @return string
     */
    public function getClassRoute(ClassReflectionInterface $classReflection);


    /**
     * @return string
     */
    public function getConstantRoute(ConstantReflectionInterface $constantReflection);


    /**
     * @return string
     */
    public function getFunctionRoute(FunctionReflectionInterface $functionReflection);


    /**
     * @param string $name
     * @return string
     */
    public function getAnnotationGroupRoute($name);


    /**
     * @return string
     */
    public function getSourceCodeRoute(NamedInterface $element);


    /**
     * @return string
     */
    public function getSourceCodeRouteWithAnchor(NamedInterface $element);
}

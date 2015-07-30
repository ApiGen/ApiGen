<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\AbstractFunctionMethodReflectionInterface;
use InvalidArgumentException;
use TokenReflection\IReflectionParameter;

abstract class ReflectionFunctionBase extends ReflectionElement implements AbstractFunctionMethodReflectionInterface
{

    /**
     * @var string Matches "array $arg"
     */
    const PARAM_ANNOTATION = '~^(?:([\\w\\\\]+(?:\\|[\\w\\\\]+)*)\\s+)?\\$(\\w+)(?:\\s+(.*))?($)~s';

    /**
     * @var array
     */
    protected $parameters;


    /**
     * {@inheritdoc}
     */
    public function getShortName()
    {
        return $this->reflection->getShortName();
    }


    /**
     * {@inheritdoc}
     */
    public function returnsReference()
    {
        return $this->reflection->returnsReference();
    }


    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        if ($this->parameters === null) {
            $this->parameters = array_map(function (IReflectionParameter $parameter) {
                return $this->reflectionFactory->createFromReflection($parameter);
            }, $this->reflection->getParameters());

            $annotations = (array) $this->getAnnotation('param');
            foreach ($annotations as $position => $annotation) {
                if (isset($this->parameters[$position])) {
                    // Standard parameter
                    continue;
                }

                $this->processAnnotation($annotation, $position);
            }
        }

        return $this->parameters;
    }


    /**
     * {@inheritdoc}
     */
    public function getParameter($key)
    {
        $parameters = $this->getParameters();

        if (isset($parameters[$key])) {
            return $parameters[$key];
        }

        foreach ($parameters as $parameter) {
            if ($parameter->getName() === $key) {
                return $parameter;
            }
        }

        throw new InvalidArgumentException(sprintf(
            'There is no parameter with name/position "%s" in function/method "%s"',
            $key,
            $this->getName()
        ));
    }


    /**
     * @param string $annotation
     * @param int $position
     */
    private function processAnnotation($annotation, $position)
    {
        if (! preg_match(self::PARAM_ANNOTATION, $annotation, $matches)) {
            return;
        }

        list(, $typeHint, $name) = $matches;

        $this->parameters[$position] = $this->reflectionFactory->createParameterMagic([
            'name' => $name,
            'position' => $position,
            'typeHint' => $typeHint,
            'defaultValueDefinition' => null,
            'unlimited' => true,
            'passedByReference' => false,
            'declaringFunction' => $this
        ]);
    }
}

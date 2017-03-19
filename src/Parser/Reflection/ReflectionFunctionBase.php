<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\AbstractFunctionMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use InvalidArgumentException;
use TokenReflection\IReflectionParameter;

abstract class ReflectionFunctionBase extends ReflectionElement implements AbstractFunctionMethodReflectionInterface
{
    /**
     * @var string Matches "array $arg"
     */
    private const PARAM_ANNOTATION = '~^(?:([\\w\\\\]+(?:\\|[\\w\\\\]+)*)\\s+)?\\$(\\w+)(?:\\s+(.*))?($)~s';

    /**
     * @var array
     */
    protected $parameters;


    public function getShortName(): string
    {
        return $this->reflection->getShortName();
    }


    public function returnsReference(): bool
    {
        return $this->reflection->returnsReference();
    }


    public function getParameters(): array
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
     * @param int|string $key
     */
    public function getParameter($key): ParameterReflectionInterface
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


    private function processAnnotation(string $annotation, int $position): void
    {
        if (! preg_match(self::PARAM_ANNOTATION, $annotation, $matches)) {
            return;
        }

        [, $typeHint, $name] = $matches;

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

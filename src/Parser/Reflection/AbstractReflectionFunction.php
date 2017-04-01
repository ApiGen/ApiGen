<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\AbstractFunctionMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use InvalidArgumentException;
use TokenReflection\IReflectionParameter;

abstract class AbstractReflectionFunction
    extends AbstractReflectionElement implements AbstractFunctionMethodReflectionInterface
{
    /**
     * @var ParameterReflectionInterface[]
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

    /**
     * @return ParameterReflectionInterface[]
     */
    public function getParameters(): array
    {
        if ($this->parameters === null) {
            $this->parameters = array_map(function (IReflectionParameter $parameter) {
                return $this->transformerCollector->transformReflectionToElement($parameter);
            }, $this->reflection->getParameters());
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
}

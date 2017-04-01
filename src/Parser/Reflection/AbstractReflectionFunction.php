<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\AbstractFunctionMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
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
}

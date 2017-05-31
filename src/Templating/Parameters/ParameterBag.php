<?php declare(strict_types=1);

namespace ApiGen\Templating\Parameters;

final class ParameterBag
{
    /**
     * @var mixed[]
     */
    private $parameters = [];

    /**
     * @param mixed[] $parameters
     */
    public function addParameters(array $parameters): void
    {
        foreach ($parameters as $key => $value) {
            $this->parameters[$key] = $value;
        }
    }

    /**
     * @return mixed[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}

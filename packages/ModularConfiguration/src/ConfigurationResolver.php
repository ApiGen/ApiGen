<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration;

use ApiGen\ModularConfiguration\Contract\ConfigurationResolverInterface;
use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;

final class ConfigurationResolver implements ConfigurationResolverInterface
{
    /**
     * @var OptionInterface[]
     */
    private $options = [];

    public function addOption(OptionInterface $option): void
    {
        $this->options[$option->getName()] = $option;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function resolveValue(string $name, $value)
    {
        if (! isset($this->options[$name])) {
            return null; // or throw exception?
        }

        return $this->options[$name]->resolveValue($value);
    }

    /**
     * @return string[]
     */
    public function getOptionNames(): array
    {
        return array_keys($this->options);
    }
}

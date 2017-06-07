<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration;

use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;
use ApiGen\ModularConfiguration\Exception\ConfigurationException;

final class ConfigurationResolver
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
     * @param mixed $value
     * @return mixed
     */
    public function resolveValue(string $name, $value)
    {
        $this->ensureOptionExists($name);

        return $this->options[$name]->resolveValue($value);
    }

    private function ensureOptionExists(string $name): void
    {
        if (! isset($this->options[$name])) {
            throw new ConfigurationException(sprintf(
                'Option "%s" was not found. Available options are: %s.',
                $name,
                array_keys($this->options)
            ));
        }
    }

    /**
     * @param mixed[] $values
     * @return mixed[]
     */
    public function resolveValuesWithDefaults(array $values): array
    {
        foreach ($this->getOptionNames() as $name) {
            $values[$name] = $this->resolveValue($name, $values[$name] ?? null);
        }

        return $values;
    }

    /**
     * @return string[]
     */
    private function getOptionNames(): array
    {
        return array_keys($this->options);
    }
}

<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration;

use ApiGen\Configuration\Exceptions\ConfigurationException;
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
        $this->ensureOptionExists($name);

        return $this->options[$name]->resolveValue($value);
    }

    /**
     * @return string[]
     */
    public function getOptionNames(): array
    {
        return array_keys($this->options);
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
}

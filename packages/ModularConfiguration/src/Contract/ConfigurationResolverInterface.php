<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Contract;

use ApiGen\ModularConfiguration\Contract\Option\OptionInterface;

interface ConfigurationResolverInterface
{
    public function addOption(OptionInterface $option): void;

    /**
     * @param mixed $value
     * @return mixed
     */
    public function resolveValue(string $name, $value);

    /**
     * @param mixed[] $values
     * @return mixed[]
     */
    public function resolveValuesWithDefaults(array $values): array;
}

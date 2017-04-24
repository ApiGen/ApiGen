<?php declare(strict_types=1);

namespace ApiGen\Configuration;

use ApiGen\ModularConfiguration\Contract\ConfigurationResolverInterface;

final class ConfigurationOptionsResolver
{
    /**
     * @var ConfigurationResolverInterface
     */
    private $configurationResolver;

    public function __construct(ConfigurationResolverInterface $configurationResolver)
    {
        $this->configurationResolver = $configurationResolver;
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    public function resolve(array $options): array
    {
        foreach ($this->configurationResolver->getOptionNames() as $name) {
            $options[$name] = $this->configurationResolver->resolveValue($name, $options[$name] ?? null);
        }

        return $options;
    }
}

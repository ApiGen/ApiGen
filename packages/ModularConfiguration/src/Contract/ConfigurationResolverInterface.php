<?php declare(strict_types=1);

namespace ApiGen\ModularConfiguration\Contract;

interface ConfigurationResolverInterface
{
    public function addConfigurationOption(ConfigurationOptionInterface $configurationOption): void;
}

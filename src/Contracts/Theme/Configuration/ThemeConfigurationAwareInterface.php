<?php

namespace ApiGen\Contracts\Theme\Configuration;

use ApiGen\Contracts\Configuration\ConfigurationInterface;

interface ThemeConfigurationAwareInterface
{

    public function setThemeConfiguration(ThemeConfigurationInterface $themeConfiguration);
}

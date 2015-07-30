<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Theme\Configuration;

use ApiGen\Contracts\Configuration\ConfigurationInterface;

interface ThemeConfigurationAwareInterface
{

    public function setThemeConfiguration(ThemeConfigurationInterface $themeConfiguration);
}

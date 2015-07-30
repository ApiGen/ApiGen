<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Console\Input;

use Symfony\Component\Console\Input\InputDefinition;

interface DefaultInputDefinitionFactoryInterface
{

    /**
     * @return InputDefinition
     */
    public function create();
}

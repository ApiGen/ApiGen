<?php

namespace ApiGen\Parser\Reflection\Parts;

use ApiGen\Contracts\Parser\Configuration\ParserConfigurationInterface;

/**
 * @property-read $isDocumented
 * @property-read ParserConfigurationInterface $configuration
 * @method bool isDeprecated()
 */
trait IsDocumentedMagic
{

    /**
     * @return bool
     */
    public function isDocumented()
    {
        return true;
    }
}

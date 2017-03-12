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
        if ($this->isDocumented === null) {
            $deprecated = $this->configuration->isDeprecatedDocumented();
            $this->isDocumented = $deprecated || ! $this->isDeprecated();
        }

        return $this->isDocumented;
    }
}

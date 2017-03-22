<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\Parts;

use ApiGen\Contracts\Parser\Configuration\ParserConfigurationInterface;

/**
 * @property-read $isDocumented
 * @property-read ParserConfigurationInterface $configuration
 * @method bool isDeprecated()
 */
trait IsDocumentedMagic
{

    public function isDocumented(): bool
    {
        return true;
    }
}

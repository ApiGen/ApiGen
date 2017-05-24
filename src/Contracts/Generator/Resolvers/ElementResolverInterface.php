<?php declare(strict_types=1);

namespace ApiGen\Contracts\Generator\Resolvers;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;

interface ElementResolverInterface
{
    /**
     * @param string|object $context
     * @return AbstractReflectionInterface|bool
     */
    public function resolveElement(string $definition, $context, ?string &$expectedName = null);
}

<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;

final class ResolverFilters extends Filters
{
    /**
     * @var ElementResolverInterface
     */
    private $elementResolver;

    public function __construct(ElementResolverInterface $elementResolver)
    {
        $this->elementResolver = $elementResolver;
    }

    /**
     * @param AbstractReflectionInterface $context
     * @param null $expectedName
     * @return AbstractReflectionInterface|bool|null
     */
    public function resolveElement(string $definition, $context, &$expectedName = null)
    {
        $reflection = $this->elementResolver->resolveElement($definition, $context, $expectedName);
        if ($reflection) {
            return $reflection;
        }

        return false;
    }
}

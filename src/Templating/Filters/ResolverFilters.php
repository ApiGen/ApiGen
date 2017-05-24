<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;

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
     * @return ClassReflectionInterface|false
     */
    public function getClass(string $className, ?string $namespace = '')
    {
        $reflection = $this->elementResolver->getClass($className, $namespace);
        if ($reflection) {
            return $reflection;
        }

        return false;
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

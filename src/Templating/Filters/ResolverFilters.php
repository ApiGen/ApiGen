<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Parser\Reflection\AbstractReflectionElement;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionInterface;

class ResolverFilters extends Filters
{
    /**
     * @var ElementResolver
     */
    private $elementResolver;

    public function __construct(ElementResolver $elementResolver)
    {
        $this->elementResolver = $elementResolver;
    }

    /**
     * @param string $className
     * @param string|null $namespace
     * @return ReflectionClass|false
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
     * @param string $definition
     * @param AbstractReflectionElement|ReflectionInterface $context
     * @param null $expectedName
     * @return AbstractReflectionElement|bool|null
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

<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionElement;

//use ApiGen\Reflection\ReflectionClass;
//use ApiGen\Reflection\ReflectionElement;

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
     * @param string|NULL $namespace
     * @return ReflectionClass|FALSE
     */
    public function getClass(string $className, ?string $namespace = null)
    {
        $reflection = $this->elementResolver->getClass($className, $namespace);
        if ($reflection) {
            return $reflection;
        }
        return false;
    }


    /**
     * @param string $definition
     * @param ReflectionElement $context
     * @param NULL $expectedName
     * @return ReflectionElement|bool|NULL
     */
    public function resolveElement(string $definition, ReflectionElement $context, &$expectedName = null)
    {
        $reflection = $this->elementResolver->resolveElement($definition, $context, $expectedName);
        if ($reflection) {
            return $reflection;
        }
        return false;
    }
}

<?php declare(strict_types=1);

namespace ApiGen\Generator\Resolvers;

use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Reflection\Contract\Reflection\Behavior\InClassInterface;
use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassPropertyReflectionInterface;

final class ElementResolver implements ElementResolverInterface
{
    /**
     * @var int[]
     */
    private $simpleTypes = [
        'boolean' => 1,
        'integer' => 1,
        'float' => 1,
        'string' => 1,
        'array' => 1,
        'object' => 1,
        'resource' => 1,
        'callback' => 1,
        'callable' => 1,
        'NULL' => 1,
        'false' => 1,
        'true' => 1,
        'mixed' => 1
    ];

    public function getClass(string $name, string $namespace = ''): ?ClassReflectionInterface
    {
        $parsedClasses = // $this->parserStorage->getClasses();

        $class = $this->findElementByNameAndNamespace($parsedClasses, $name, $namespace);
        if ($class && $class->isDocumented()) {
            return $class;
        }

        return null;
    }

    /**
     * @return FunctionReflectionInterface|ClassMethodReflectionInterface|null
     */
    public function getFunction(string $name, string $namespace = '')
    {
        $parsedFunctions = // $this->parserStorage->getFunctions();
        $function = $this->findElementByNameAndNamespace($parsedFunctions, $name, $namespace);
        if ($function && $function->isDocumented()) {
            return $function;
        }

        return null;
    }

    /**
     * @param object|string $reflectionElement
     * @return ClassReflectionInterface|ClassConstantReflectionInterface|FunctionReflectionInterface|ClassMethodReflectionInterface|ClassPropertyReflectionInterface|null
     */
    public function resolveElement(string $definition, $reflectionElement, ?string &$expectedName = null)
    {
        if ($this->isSimpleType($definition)) {
            return null;
        }

        // @todo hotfix
        if (class_exists($definition, false)) {
            return $reflectionElement;
        }

        $originalContext = $reflectionElement;
        $reflectionElement = $this->correctContextForParameterOrClassMember($reflectionElement);

        if ($reflectionElement === null) {
            return null;
        }

        // self, static, $this references
        if ($definition === 'self' || $definition === 'static' || $definition === '$this') {
            return $reflectionElement instanceof ClassReflectionInterface ? $reflectionElement : null;
        }

        $definitionBase = substr($definition, 0, strcspn($definition, '\\:'));

        $className = Resolver::resolveClassFQN($definition, [], $reflectionElement->getNamespaceName());

        $resolved = $this->resolveIfParsed($definition, $reflectionElement);
        if ($resolved) {
            return $resolved;
        }

        if (! empty($definitionBase) && isset($namespaceAliases[$definitionBase]) && $definition !== $className) {
            // Aliased class
            $expectedName = $className;

            if (strpos($className, ':') === false) {
                return $this->getClass($className, $reflectionElement->getNamespaceName());
            }

            $definition = $className;
        }

        $position = $this->getPositionFromDefinition($definition);
        if ($reflectionElement instanceof ClassReflectionInterface && $position) {
            $reflectionElement = $this->resolveContextForClassProperty($definition, $reflectionElement, $position);
            $definition = substr($definition, $position + 2);
        } elseif ($originalContext instanceof ParameterReflectionInterface) {
            return null;
        }

        if (! $this->isContextUsable($reflectionElement)) {
            return null;
        }

        return $this->resolveIfInContext($definition, $reflectionElement);
    }

    /**
     * @param ClassReflectionInterface|ParameterReflectionInterface|FunctionReflectionInterface $reflectionElement
     * @return ClassReflectionInterface|FunctionReflectionInterface
     */
    private function correctContextForParameterOrClassMember($reflectionElement)
    {
        if ($reflectionElement instanceof ParameterReflectionInterface
            && $reflectionElement->getDeclaringClassName() === ''
        ) {
            return $this->getFunction($reflectionElement->getDeclaringFunctionName());
        }

        if ($reflectionElement instanceof InClassInterface) {
            return $this->getClass($reflectionElement->getDeclaringClassName());
        }

        return $reflectionElement;
    }

    private function resolveContextForSelfProperty(
        string $definition,
        int $pos,
        ReflectionInterface $reflectionElement
    ): ?ClassReflectionInterface {
        $class = $this->getClass(substr($definition, 0, $pos), $reflectionElement->getNamespaceName());
        if ($class === null) {
            $fqnName = Resolver::resolveClassFQN(
                substr($definition, 0, $pos),
                [],
                $reflectionElement->getNamespaceName()
            );
            $class = $this->getClass($fqnName);
        }

        return $class;
    }

    private function isSimpleType(string $definition): bool
    {
        return empty($definition) || isset($this->simpleTypes[$definition]);
    }

    /**
     * @return ClassReflectionInterface|ClassConstantReflectionInterface|FunctionReflectionInterface|null
     */
    private function resolveIfParsed(string $definition, ReflectionInterface $reflectionElement)
    {
        $definition = $this->removeEndBrackets($definition);

        $class = $class = $this->getClass($definition, $reflectionElement->getNamespaceName());
        if ($class) {
            return $class;
        }

        $function = $this->getFunction($definition, $reflectionElement->getNamespaceName());
        if ($function) {
            return $function;
        }

        return null;
    }

    /**
     * @return ClassConstantReflectionInterface|ClassMethodReflectionInterface|ClassPropertyReflectionInterface|null
     */
    private function resolveIfInContext(string $definition, ClassReflectionInterface $classReflection)
    {
        $definition = $this->removeEndBrackets($definition);
        $definition = $this->removeStartDollar($definition);

        if ($classReflection->hasProperty($definition)) {
            return $classReflection->getProperty($definition);
        }

        if ($classReflection->hasMethod($definition)) {
            return $classReflection->getMethod($definition);
        }

        if ($classReflection->hasConstant($definition)) {
            return $classReflection->getConstant($definition);
        }

        return null;
    }

    private function removeEndBrackets(string $definition): string
    {
        if (substr($definition, -2) === '()') {
            return substr($definition, 0, -2);
        }

        return $definition;
    }

    private function removeStartDollar(string $definition): string
    {
        if ($definition[0] === '$') {
            return substr($definition, 1);
        }

        return $definition;
    }

    private function resolveContextForClassProperty(
        string $definition,
        ClassReflectionInterface $reflectionClass,
        int $position
    ): ?ClassReflectionInterface {
        // Class::something or Class->something
        $parentClassName = $reflectionClass->getParentClassName();

        if (strpos($definition, 'parent::') === 0 && $parentClassName) {
            return $this->getClass($parentClassName);
        }

        if (strpos($definition, 'self::') !== 0) {
            return $this->resolveContextForSelfProperty($definition, $position, $reflectionClass);
        }

        return $reflectionClass;
    }

    /**
     * @param object|string|null $reflectionElement
     */
    private function isContextUsable($reflectionElement): bool
    {
        if ($reflectionElement === null || $reflectionElement instanceof ClassConstantReflectionInterface
            || $reflectionElement instanceof FunctionReflectionInterface
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed[] $elements
     * @return mixed|ReflectionInterface
     */
    private function findElementByNameAndNamespace(array $elements, string $name, string $namespace)
    {
        $namespacedName = $namespace . '\\' . $name;
        if (isset($elements[$namespacedName])) {
            return $elements[$namespacedName];
        }

        $shortName = ltrim($name, '\\');
        if (isset($elements[$shortName])) {
            return $elements[$shortName];
        }

        return null;
    }

    private function getPositionFromDefinition(string $definition): int
    {
        $pos = strpos($definition, '::');
        if ($pos) {
            return $pos;
        }

        $pos = strpos($definition, '->');
        if ($pos) {
            return $pos;
        }

        return 0;
    }
}

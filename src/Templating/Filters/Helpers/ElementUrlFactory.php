<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters\Helpers;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Configuration\Theme\ThemeConfigOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Templating\Filters\Filters;

final class ElementUrlFactory
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param ElementReflectionInterface|string $element
     */
    public function createForElement($element): ?string
    {
        if ($element instanceof ClassReflectionInterface) {
            return $this->createForClass($element);
        } elseif ($element instanceof MethodReflectionInterface) {
            return $this->createForMethod($element);
        } elseif ($element instanceof PropertyReflectionInterface) {
            return $this->createForProperty($element);
        } elseif ($element instanceof ConstantReflectionInterface) {
            return $this->createForConstant($element);
        } elseif ($element instanceof FunctionReflectionInterface) {
            return $this->createForFunction($element);
        }

        return null;
    }

    /**
     * @param string|ClassReflectionInterface $class
     */
    public function createForClass($class): string
    {
        $className = $class instanceof ClassReflectionInterface ? $class->getName() : $class;
        return sprintf(
            $this->configuration->getOption(ConfigurationOptions::TEMPLATE)['templates']['class']['filename'],
            Filters::urlize($className)
        );
    }

    public function createForMethod(MethodReflectionInterface $method, ?ClassReflectionInterface $class = null): string
    {
        $className = $class !== null ? $class->getName() : $method->getDeclaringClassName();
        return $this->createForClass($className) . '#_'
            . ($method->getOriginalName() ?: $method->getName());
    }

    public function createForProperty(
        PropertyReflectionInterface $property,
        ?ClassReflectionInterface $class = null
    ): string {
        $className = $class !== null ? $class->getName() : $property->getDeclaringClassName();
        return $this->createForClass($className) . '#$' . $property->getName();
    }

    public function createForConstant(ConstantReflectionInterface $constant): string
    {
        $className = $constant->getDeclaringClassName();

        return $this->createForClass($className) . '#' . $constant->getName();
    }

    public function createForFunction(FunctionReflectionInterface $function): string
    {
        return sprintf(
            $this->configuration->getOption(ConfigurationOptions::TEMPLATE)['templates']['function']['filename'],
            Filters::urlize($function->getName())
        );
    }

    public function createForAnnotationGroup(string $name): string
    {
        return sprintf(
            $this->configuration->getOption(
                ConfigurationOptions::TEMPLATE
            )['templates'][ThemeConfigOptions::ANNOTATION_GROUP]['filename'],
            Filters::urlize($name)
        );
    }
}

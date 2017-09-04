<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\AccessLevels;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\ParentClass;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\RichInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

abstract class AbstractReflectionClassTestCase extends AbstractParserAwareTestCase
{
    /**
     * @var ClassReflectionInterface
     */
    protected $reflectionClass;

    /**
     * @var ClassReflectionInterface
     */
    protected $reflectionClassOfParent;

    /**
     * @var InterfaceReflectionInterface
     */
    protected $interfaceReflection;

    protected function setUp(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/Source']);
        $this->parser->parse();

        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->reflectionClass = $classReflections[AccessLevels::class];
        $this->reflectionClassOfParent = $classReflections[ParentClass::class];

        $interfaceReflections = $this->reflectionStorage->getInterfaceReflections();
        $this->interfaceReflection = $interfaceReflections[RichInterface::class];
    }
}

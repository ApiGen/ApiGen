<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Trait_\TraitReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Trait_\TraitReflection\ComposeTraitSource\BaseClass;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ComposedTraitTest extends AbstractParserAwareTestCase
{
    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    protected function setUp(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/ComposeTraitSource']);
        $this->parser->parse();

        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->classReflection = $classReflections[BaseClass::class];
    }

    public function test(): void
    {
        $this->assertCount(2, $this->classReflection->getTraits());
        $this->assertCount(1, $this->classReflection->getMethods());
        $this->assertCount(1, $this->classReflection->getOwnMethods());
    }
}

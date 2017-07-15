<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\DocBlock;

use ApiGen\Reflection\DocBlock\DocBlockFactory;
use ApiGen\Reflection\Tests\DocBlock\DocBlockFactorySource\ClassWithSeeAnnotation;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class DocBlockFactoryTest extends AbstractContainerAwareTestCase
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    protected function setUp(): void
    {
        $this->docBlockFactory = $this->container->get(DocBlockFactory::class);
    }

    public function test(): void
    {
        $classWithSeeAnnotation = ReflectionClass::createFromName(ClassWithSeeAnnotation::class);

        $docBlock = $this->docBlockFactory->createFromBetterReflection($classWithSeeAnnotation);

        $this->assertInstanceOf(DocBlock::class, $docBlock);

        $this->assertCount(1, $docBlock->getTagsByName('see'));
        $this->assertCount(0, $docBlock->getTagsByName('link'));
        $this->assertCount(1, $docBlock->getTagsByName('param'));
    }
}

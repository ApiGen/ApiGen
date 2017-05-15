<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Tests;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\StringRouting\Router;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class SourceCodeRouteTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Router
     */
    private $router;

    protected function setUp(): void
    {
        $this->router = $this->container->getByType(Router::class);
    }

    public function testClassReflection(): void
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('getName')
            ->willReturn('someName');

        $this->assertSame('source-class-someName.html', $this->router->buildRoute('sourceCode', $classReflectionMock));
    }

    public function testTraitReflection(): void
    {
        $traitReflectionMock = $this->createMock(TraitReflectionInterface::class);
        $traitReflectionMock->method('getName')
            ->willReturn('someName');

        $this->assertSame('source-trait-someName.html', $this->router->buildRoute('sourceCode', $traitReflectionMock));
    }
}

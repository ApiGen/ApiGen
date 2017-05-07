<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Templating\Filters\ResolverFilters;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use PHPUnit_Framework_MockObject_MockObject;

final class ResolverFiltersTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ResolverFilters
     */
    private $resolverFilters;

    protected function setUp(): void
    {
        $this->resolverFilters = $this->container->getByType(ResolverFilters::class);

        $classReflectionMock = $this->createClassReflectionMock();

        /** @var ParserStorageInterface $parserStorage */
        $parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $parserStorage->setClasses([
            'SomeClass' => $classReflectionMock
        ]);
    }

    public function testGetClass(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->resolverFilters->getClass('SomeClass'));
    }

    public function testGetClassForNonExistingClass(): void
    {
        $this->assertFalse($this->resolverFilters->getClass('NotExistingClass'));
    }

    public function testResolveElementForNonExistingElement(): void
    {
        $reflectionElementMock = $this->createMock(ClassReflectionInterface::class);
        $this->assertFalse($this->resolverFilters->resolveElement('NonExistingElement', $reflectionElementMock));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ClassReflectionInterface
     */
    private function createClassReflectionMock()
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('isDocumented')
            ->willReturn(true);

        return $classReflectionMock;
    }
}

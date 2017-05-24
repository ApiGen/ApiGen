<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Templating\Filters\ResolverFilters;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\Templating\Filters\ResolveFilterSource\SomeClassToResolve;

final class ResolverFiltersTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ResolverFilters
     */
    private $resolverFilters;

    protected function setUp(): void
    {
        $this->resolverFilters = $this->container->getByType(ResolverFilters::class);

        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/ResolveFilterSource']);
    }

    public function testNonExistingElementOnClass(): void
    {
        $reflectionElementMock = $this->createMock(ClassReflectionInterface::class);
        $this->assertFalse($this->resolverFilters->resolveElement('NonExistingElement', $reflectionElementMock));
    }
}

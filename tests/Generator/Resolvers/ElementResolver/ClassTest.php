<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;

final class ClassTest extends AbstractElementResolverTest
{
    /**
     * @var string
     */
    private $namespace = 'ApiGen\Tests\Generator\Resolvers\ElementResolver\Source';

    public function testGetClass(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $element = $this->elementResolver->getClass('SomeClass');

        $element2 = $this->elementResolver->getClass('SomeClass', $this->namespace);
        $this->assertInstanceOf(AbstractReflectionInterface::class, $element2);

        $this->assertNotSame($element, $element2);
    }

    public function testGetClassNotExisting(): void
    {
        $this->assertNull($this->elementResolver->getClass('NotExistingClass'));
    }
}

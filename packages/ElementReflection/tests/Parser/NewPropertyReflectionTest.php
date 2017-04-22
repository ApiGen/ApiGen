<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Tests\Parser;

use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\ElementReflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

/**
 * Mirror to Function test @see \ApiGen\Parser\Tests\ParserTest
 */
final class NewPropertyReflectionTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PropertyReflectionInterface
     */
    private $propertyReflection;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->getByType(Parser::class);
        $parser->parseDirectories([__DIR__ . '/../../../../tests/Parser/Parser/ParserSource']);

        $classReflections = $parser->getClassReflections();
        $classReflection = array_pop($classReflections);

        $propertyReflections = $classReflection->getProperties();
        $this->propertyReflection = array_shift($propertyReflections);
    }

    /**
     * @todo
     */
    public function testLines(): void
    {
        $this->assertNull($this->propertyReflection);
    }
}

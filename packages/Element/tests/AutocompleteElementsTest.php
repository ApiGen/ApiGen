<?php declare(strict_types=1);

namespace ApiGen\Element;

use ApiGen\Element\Contract\AutocompleteElementsInterface;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class AutocompleteElementsTest extends AbstractContainerAwareTestCase
{
    /**
     * @var AutocompleteElementsInterface
     */
    private $autocompleteElements;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        $this->autocompleteElements = $this->container->getByType(AutocompleteElements::class);
    }

    public function testGetElementsClasses(): void
    {
        $elements = $this->autocompleteElements->getElements();

        $this->assertSame([
            ['c', SomeClass::class],
            ['p', SomeClass::class . '::$someProperty'],
            ['m', SomeClass::class . '::SomeMethod()'],
            ['c', SomeInterface::class],
            ['c', SomeOtherClass::class],
            ['p', SomeOtherClass::class . '::$someProperty'],
            ['c', YetAnotherClass::class],
            ['f', 'SomeNamespace\someAloneFunction()']
        ], $elements);
    }
}

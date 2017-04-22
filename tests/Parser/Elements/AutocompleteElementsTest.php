<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Parser\Elements\AutocompleteElements;
use ApiGen\Parser\Tests\Parser\ParserSource\SomeClass;
use ApiGen\Parser\Tests\Parser\ParserSource\SomeInterface;
use ApiGen\Parser\Tests\ParserSource\SomeOtherClass;
use ApiGen\Parser\Tests\ParserSource\YetAnotherClass;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class AutocompleteElementsTest extends AbstractContainerAwareTestCase
{
    /**
     * @var AutocompleteElements
     */
    private $autocompleteElements;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/../Parser/ParserSource']);

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

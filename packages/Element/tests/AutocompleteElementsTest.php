<?php declare(strict_types=1);

namespace ApiGen\Element;

use ApiGen\Element\Contract\AutocompleteElementsInterface;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class AutocompleteElementsTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private $namespacePrefix = 'ApiGen\Element\Tests\Namespaces';

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
            ['f', $this->namespacePrefix . '\namespacedFunction()'],
            ['c', 'NoneNamespacedClass'],
            ['c', $this->namespacePrefix . '\NamespacedClass'],
            ['i', $this->namespacePrefix . '\SubNamespace\SubNamespacedInterface'],
            ['t', $this->namespacePrefix . '\SubNamespace\SubNamespacedTrait'],
        ], $elements);
    }
}

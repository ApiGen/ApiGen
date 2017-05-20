<?php declare(strict_types=1);

namespace ApiGen\Element\Tests;

use ApiGen\Element\AutocompleteElements;
use ApiGen\Element\Contract\AutocompleteElementsInterface;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class AutocompleteElementsTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private $namespacePrefix = 'ApiGen\Element\Tests\Namespaces\Source';

    /**
     * @var AutocompleteElementsInterface
     */
    private $autocompleteElements;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Namespaces/Source']);

        $this->autocompleteElements = $this->container->getByType(AutocompleteElements::class);
    }

    public function testGetElementsClasses(): void
    {
        $elements = $this->autocompleteElements->getElements();
        $this->assertCount(5, $elements);

        $this->assertContains($this->namespacePrefix . '\namespacedFunction()', $elements);
        $this->assertContains('NoneNamespacedClass', $elements);
        $this->assertContains($this->namespacePrefix . '\NamespacedClass', $elements);
        $this->assertContains($this->namespacePrefix . '\SubNamespace\SubNamespacedInterface', $elements);
        $this->assertContains($this->namespacePrefix . '\SubNamespace\SubNamespacedTrait', $elements);
    }
}

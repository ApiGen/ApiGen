<?php declare(strict_types=1);

namespace ApiGen\Element\Tests;

use ApiGen\Element\AutocompleteElements;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class AutocompleteElementsTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private $namespacePrefix = 'ApiGen\Element\Tests\ReflectionCollector\NamespaceReflectionCollectorSource';

    /**
     * @var AutocompleteElements
     */
    private $autocompleteElements;

    /**
     * @var ReflectionRoute
     */
    private $reflectionRoute;

    /**
     * @var ReflectionStorage
     */
    private $reflectionStorage;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseDirectories([__DIR__ . '/ReflectionCollector/NamespaceReflectionCollectorSource']);

        $this->autocompleteElements = $this->container->get(AutocompleteElements::class);
        $this->reflectionStorage = $this->container->get(ReflectionStorage::class);
        $this->reflectionRoute = $this->container->get(ReflectionRoute::class);
    }

    /**
     * @param string[][]  $items
     */
    private function autocompleteHasItem(string $pattern, array $items, string $what = 'label'): bool
    {
        foreach ($items as $item) {
            if ($item[$what] === $pattern) {
                return true;
            }
        }

        return false;
    }

    public function testGetElementsClasses(): void
    {
        $autocompleteElements = $this->autocompleteElements->getElements();
        $this->assertCount(8, $autocompleteElements);

        $this->assertTrue($this->autocompleteHasItem(
            $this->namespacePrefix,
            $autocompleteElements
        ));
        $this->assertTrue($this->autocompleteHasItem(
            $this->namespacePrefix . '\SubNamespace',
            $autocompleteElements
        ));

        $this->assertTrue($this->autocompleteHasItem(
            'none',
            $autocompleteElements
        ));
        $this->assertTrue($this->autocompleteHasItem(
            'NoneNamespacedClass',
            $autocompleteElements
        ));

        $this->assertTrue($this->autocompleteHasItem(
            $this->namespacePrefix . '\namespacedFunction()',
            $autocompleteElements
        ));
        $this->assertTrue($this->autocompleteHasItem(
            $this->namespacePrefix . '\NamespacedClass',
            $autocompleteElements
        ));
        $this->assertTrue($this->autocompleteHasItem(
            $this->namespacePrefix . '\SubNamespace\SubNamespacedInterface',
            $autocompleteElements
        ));
        $this->assertTrue($this->autocompleteHasItem(
            $this->namespacePrefix . '\SubNamespace\SubNamespacedTrait',
            $autocompleteElements
        ));

        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->assertCount(2, $classReflections);

        foreach ($classReflections as $classReflection) {
            $this->assertTrue($this->autocompleteHasItem(
                $this->reflectionRoute->constructUrl($classReflection),
                $autocompleteElements, 'file'
            ));
        }
    }
}

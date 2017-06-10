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

    public function testGetElementsClasses(): void
    {
        $autocompleteElements = $this->autocompleteElements->getElements();
        $this->assertCount(8, $autocompleteElements);

        $this->assertArrayHasKey($this->namespacePrefix, $autocompleteElements);
        $this->assertArrayHasKey($this->namespacePrefix . '\SubNamespace', $autocompleteElements);

        $this->assertArrayHasKey('none', $autocompleteElements);
        $this->assertArrayHasKey('NoneNamespacedClass', $autocompleteElements);

        $this->assertArrayHasKey($this->namespacePrefix . '\namespacedFunction()', $autocompleteElements);
        $this->assertArrayHasKey($this->namespacePrefix . '\NamespacedClass', $autocompleteElements);
        $this->assertArrayHasKey($this->namespacePrefix . '\SubNamespace\SubNamespacedInterface', $autocompleteElements);
        $this->assertArrayHasKey($this->namespacePrefix . '\SubNamespace\SubNamespacedTrait', $autocompleteElements);

        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->assertCount(2, $classReflections);

        foreach ($classReflections as $classReflection) {
            $this->assertContains($this->reflectionRoute->constructUrl($classReflection), $autocompleteElements);
        }
    }
}

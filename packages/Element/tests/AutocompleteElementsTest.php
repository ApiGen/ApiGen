<?php declare(strict_types=1);

namespace ApiGen\Element\Tests;

use ApiGen\Element\Contract\AutocompleteElementsInterface;
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
     * @var AutocompleteElementsInterface
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
        $parser = $this->container->getByType(Parser::class);
        $parser->parseDirectories([__DIR__ . '/ReflectionCollector/NamespaceReflectionCollectorSource']);

        $this->autocompleteElements = $this->container->getByType(AutocompleteElementsInterface::class);

        $this->reflectionStorage = $this->container->getByType(ReflectionStorage::class);
        $this->reflectionRoute = $this->container->getByType(ReflectionRoute::class);
    }

    public function testGetElementsClasses(): void
    {
        $autocompleteElements = $this->autocompleteElements->getElements();
        $this->assertCount(5, $autocompleteElements);

        $this->assertContains($this->namespacePrefix . '\namespacedFunction()', $autocompleteElements);
        $this->assertContains('NoneNamespacedClass', $autocompleteElements);
        $this->assertContains($this->namespacePrefix . '\NamespacedClass', $autocompleteElements);
        $this->assertContains($this->namespacePrefix . '\SubNamespace\SubNamespacedInterface', $autocompleteElements);
        $this->assertContains($this->namespacePrefix . '\SubNamespace\SubNamespacedTrait', $autocompleteElements);

        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->assertCount(2, $classReflections);

        foreach ($classReflections as $classReflection) {
            $this->assertArrayHasKey($this->reflectionRoute->constructUrl($classReflection), $autocompleteElements);
        }
    }
}

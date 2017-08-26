<?php declare(strict_types=1);

namespace ApiGen\Element\Tests;

use ApiGen\Element\AutocompleteElements;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use NoneNamespacedClass;

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
        $parser->parseFilesAndDirectories([__DIR__ . '/ReflectionCollector/NamespaceReflectionCollectorSource']);

        $this->autocompleteElements = $this->container->get(AutocompleteElements::class);
        $this->reflectionStorage = $this->container->get(ReflectionStorage::class);
        $this->reflectionRoute = $this->container->get(ReflectionRoute::class);
    }

    public function testCounts(): void
    {
        $autocompleteElements = $this->autocompleteElements->getElements();
        $this->assertCount(8, $autocompleteElements);

        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->assertCount(2, $classReflections);
    }

    /**
     * @dataProvider provideDataForLabelsTest
     */
    public function testLabels(string $label): void
    {
        $autocompleteElements = $this->autocompleteElements->getElements();

        $hasLabel = false;
        foreach ($autocompleteElements as $item) {
            if ($item['label'] === $label) {
                $hasLabel = true;
            }
        }

        $this->assertTrue($hasLabel);
    }

    /**
     * @return string[][]
     */
    public function provideDataForLabelsTest(): array
    {
        return [
            [$this->namespacePrefix],
            [$this->namespacePrefix . '\SubNamespace'],
            ['none'],
            [NoneNamespacedClass::class],
            [$this->namespacePrefix . '\namespacedFunction()'],
            [$this->namespacePrefix . '\NamespacedClass'],
            [$this->namespacePrefix . '\SubNamespace\SubNamespacedInterface'],
            [$this->namespacePrefix . '\SubNamespace\SubNamespacedTrait'],
        ];
    }

    public function testFilePaths(): void
    {
        $classReflections = $this->reflectionStorage->getClassReflections();
        $autocompleteElements = $this->autocompleteElements->getElements();

        foreach ($classReflections as $classReflection) {
            $found = false;
            $file = $this->reflectionRoute->constructUrl($classReflection);
            foreach ($autocompleteElements as $item) {
                if ($item['file'] === $file) {
                    $found = true;
                }
            }

            $this->assertTrue($found);
        }
    }
}

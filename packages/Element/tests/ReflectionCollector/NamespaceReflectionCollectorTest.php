<?php declare(strict_types=1);

namespace ApiGen\Element\Tests\ReflectionCollector;

use ApiGen\Element\ReflectionCollector\NamespaceReflectionCollector;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class NamespaceReflectionCollectorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private $namespacePrefix = 'ApiGen\Element\Tests\ReflectionCollector\NamespaceReflectionCollectorSource';

    /**
     * @var NamespaceReflectionCollector
     */
    private $namespaceReflectionCollector;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/NamespaceReflectionCollectorSource']);

        $this->namespaceReflectionCollector = $this->container->getByType(NamespaceReflectionCollector::class);
    }

    public function testSort(): void
    {
        $namespaces = $this->namespaceReflectionCollector->getNamespaces();
        $this->assertCount(3, $namespaces);

        $this->assertSame([
            $this->namespacePrefix,
            $this->namespacePrefix . '\SubNamespace',
            NamespaceReflectionCollector::NO_NAMESPACE,
        ], $namespaces);
    }

    public function testFetchFromNamespace(): void
    {
        $this->assertCount(1, $this->namespaceReflectionCollector->getClassReflections($this->namespacePrefix));
        $this->assertCount(0, $this->namespaceReflectionCollector->getTraitReflections($this->namespacePrefix));
        $this->assertCount(1, $this->namespaceReflectionCollector->getFunctionReflections($this->namespacePrefix));
        $this->assertCount(0, $this->namespaceReflectionCollector->getInterfaceReflections($this->namespacePrefix));

        $desiredNamespace = $this->namespacePrefix . '\SubNamespace';
        $this->assertCount(0, $this->namespaceReflectionCollector->getClassReflections($desiredNamespace));
        $this->assertCount(1, $this->namespaceReflectionCollector->getTraitReflections($desiredNamespace));
        $this->assertCount(0, $this->namespaceReflectionCollector->getFunctionReflections($desiredNamespace));
        $this->assertCount(1, $this->namespaceReflectionCollector->getInterfaceReflections($desiredNamespace));
    }

    public function testNoneNamespace(): void
    {
        $this->assertCount(
            1,
            $this->namespaceReflectionCollector->getClassReflections(NamespaceReflectionCollector::NO_NAMESPACE)
        );
    }
}

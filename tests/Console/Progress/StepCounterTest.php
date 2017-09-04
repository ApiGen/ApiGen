<?php declare(strict_types=1);

namespace ApiGen\Tests\Console\Helper;

use ApiGen\Console\Progress\StepCounter;
use ApiGen\Element\Namespace_\ParentEmptyNamespacesResolver;
use ApiGen\Element\ReflectionCollector\NamespaceReflectionCollector;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class StepCounterTest extends AbstractParserAwareTestCase
{
    public function testStepCount(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/Source']);
        $this->parser->parse();
        $namespaceReflectionCollector = $this->container->get(NamespaceReflectionCollector::class);

        $stepCounter = new StepCounter(
            $this->reflectionStorage,
            $namespaceReflectionCollector,
            new ParentEmptyNamespacesResolver
        );

        $count = 2;    // index.html + elementlist.js
        $count += 5;   // classes.html + exceptions.html + interfaces.html + traits.html + functions.html

        $count += 3;   // Namespace: none, EmptyNamespace, EmptyNamespace\MyNamespace

        $count += 2;   // EmptyNamespace\MyNamespace\SomeClass + source
        $count += 4;   // EmptyNamespace\MyNamespace\SomeException + source, \Exception, \Throwable
        $count += 2;   // EmptyNamespace\MyNamespace\SomeFunction() + source
        $count += 2;   // EmptyNamespace\MyNamespace\SomeInterface + source
        $count += 2;   // EmptyNamespace\MyNamespace\SomeTrait + source

        $this->assertSame($count, $stepCounter->getStepCount());
    }
}

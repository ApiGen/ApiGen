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
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);
        $namespaceReflectionCollector = $this->container->get(NamespaceReflectionCollector::class);

        $stepCounter = new StepCounter(
            $this->reflectionStorage,
            $namespaceReflectionCollector,
            new ParentEmptyNamespacesResolver()
        );

        $count = 2;    // index.html + elementlist.js
        $count += 5;   // classes.html + exceptions.html + interfaces.html + traits.html + functions.html
        $count += 10;  // class, exception, interface, trait and function + their sources
        $count ++;     // EmptyNamespace
        $count ++;     // MyNamespace

        $this->assertSame($count, $stepCounter->getStepCount());
    }
}

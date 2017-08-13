<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests;

use ApiGen\Reflection\TransformerCollector;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class TransformerCollectorTest extends AbstractContainerAwareTestCase
{
    public function testClassSorting(): void
    {
        $reflections = [
            $this->createClassReflectionMock('ClassZ'),
            $this->createClassReflectionMock('ClassA'),
            $this->createClassReflectionMock('ClassX'),
            $this->createClassReflectionMock('ClassB'),
        ];

        $transformerCollector = $this->container->get(TransformerCollector::class);
        $elements = $transformerCollector->transformGroup($reflections);

        $this->assertSame(['ClassA', 'ClassB', 'ClassX', 'ClassZ'], array_keys($elements));
    }

    /**
     * @return object
     */
    private function createClassReflectionMock(string $name)
    {
        $reflection = $this->createMock(ReflectionClass::class);
        $reflection->method('getName')->willReturn($name);
        $reflection->method('isTrait')->willReturn(false);
        $reflection->method('isInterface')->willReturn(false);

        return $reflection;
    }
}

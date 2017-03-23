<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Tests\MethodInvoker;

final class FindElementByNameTest extends AbstractElementResolverTest
{
    /**
     * @dataProvider getFindElementByNameAndNamespaceData()
     */
    public function testFindElementByNameAndNamespace(string $name, string $namespace, ?int $expected): void
    {
        $elements = [
            'ApiGen' => 1,
            'ApiGen\SomeClass' => 2
        ];

        $this->assertSame(
            $expected,
            MethodInvoker::callMethodOnObject(
                $this->elementResolver,
                'findElementByNameAndNamespace',
                [$elements, $name, $namespace]
            )
        );
    }

    /**
     * @return string[]
     */
    public function getFindElementByNameAndNamespaceData(): array
    {
        return [
            ['ApiGen', '', 1],
            ['SomeClass', 'ApiGen', 2],
            ['SomeClass', 'ApiGen\Generator', null],
            ['\\ApiGen', '', 1]
        ];
    }
}

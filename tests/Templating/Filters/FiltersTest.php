<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Templating\Filters\Filters;
use ApiGen\Tests\MethodInvoker;
use ApiGen\Tests\Templating\Filters\FiltersSource\FooFilters;
use PHPUnit\Framework\TestCase;

final class FiltersTest extends TestCase
{
    /**
     * @var Filters
     */
    private $filters;

    protected function setUp(): void
    {
        $this->filters = new FooFilters;
    }

    public function testLoader(): void
    {
        $this->assertSame('Filtered: foo', $this->filters->loader('bazFilter', 'foo'));
        $this->assertNull($this->filters->loader('nonExisting'));
    }

    /**
     * @dataProvider typeNameProvider()
     */
    public function testGetTypeName(string $name, string $expectedName): void
    {
        $this->assertSame(
            $expectedName,
            MethodInvoker::callMethodOnObject($this->filters, 'getTypeName', [$name])
        );
    }

    /**
     * @return string[]
     */
    public function typeNameProvider(): array
    {
        return [
            ['bool', 'boolean'],
            ['double', 'float'],
            ['void', ''],
            ['FALSE', 'false'],
            ['TRUE', 'true'],
            ['NULL', 'null'],
            ['callback', 'callable'],
            ['integer', 'integer'],
            ['boolean', 'boolean'],
            ['My\\Class', 'My\\Class'],
            ['\\My\\Class', 'My\\Class']
        ];
    }

    public function testGetTypeNameWithTrimOff(): void
    {
        $this->assertSame(
            '\\Namespace',
            MethodInvoker::callMethodOnObject($this->filters, 'getTypeName', ['\\Namespace', false])
        );
    }

    public function testUrlize(): void
    {
        $this->assertSame(
            'Some.class',
            MethodInvoker::callMethodOnObject($this->filters, 'urlize', ['Some class'])
        );
    }

    public function testUrl(): void
    {
        $this->assertSame(
            'Some%20class',
            MethodInvoker::callMethodOnObject($this->filters, 'url', ['Some class'])
        );
    }
}

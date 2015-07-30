<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Templating\Filters\Filters;
use ApiGen\Tests\MethodInvoker;
use ApiGen\Tests\Templating\Filters\FiltersSource\FooFilters;
use PHPUnit_Framework_TestCase;

class FiltersTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Filters
     */
    private $filters;


    protected function setUp()
    {
        $this->filters = new FooFilters;
    }


    public function testLoader()
    {
        $this->assertSame('Filtered: foo', $this->filters->loader('bazFilter', 'foo'));
        $this->assertNull($this->filters->loader('nonExisting'));
    }


    /**
     * @dataProvider typeNameProvider
     */
    public function testGetTypeName($name, $expectedName)
    {
        $this->assertSame(
            $expectedName,
            MethodInvoker::callMethodOnObject($this->filters, 'getTypeName', [$name])
        );
    }


    /**
     * @return array[]
     */
    public function typeNameProvider()
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


    public function testGetTypeNameWithTrimOff()
    {
        $this->assertSame(
            '\\Namespace',
            MethodInvoker::callMethodOnObject($this->filters, 'getTypeName', ['\\Namespace', false])
        );
    }


    public function testUrlize()
    {
        $this->assertSame(
            'Some.class',
            MethodInvoker::callMethodOnObject($this->filters, 'urlize', ['Some class'])
        );
    }


    public function testUrl()
    {
        $this->assertSame(
            'Some%20class',
            MethodInvoker::callMethodOnObject($this->filters, 'url', ['Some class'])
        );
    }
}

<?php

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Parser\Elements\Elements;
use PHPUnit\Framework\TestCase;

class ElementsTest extends TestCase
{

    /**
     * @var Elements
     */
    private $elements;


    protected function setUp()
    {
        $this->elements = new Elements;
    }


    public function testGetClassTypeList()
    {
        $this->assertSame(
            ['classes', 'exceptions', 'interfaces', 'traits'],
            $this->elements->getClassTypeList()
        );
    }


    public function testGetAll()
    {
        $this->assertSame(
            ['classes', 'constants', 'exceptions', 'functions', 'interfaces', 'traits'],
            $this->elements->getAll()
        );
    }


    public function testGetEmptyList()
    {
        $this->assertSame(
            [
                'classes' => [],
                'constants' => [],
                'exceptions' => [],
                'functions' => [],
                'interfaces' => [],
                'traits' => []
            ],
            $this->elements->getEmptyList()
        );
    }
}

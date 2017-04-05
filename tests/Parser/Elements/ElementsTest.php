<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Parser\Elements\Elements;
use PHPUnit\Framework\TestCase;

class ElementsTest extends TestCase
{
    /**
     * @var Elements
     */
    private $elements;

    protected function setUp(): void
    {
        $this->elements = new Elements;
    }

    public function testGetClassTypeList(): void
    {
        $this->assertSame(
            ['classes', 'exceptions', 'interfaces', 'traits'],
            $this->elements->getClassTypeList()
        );
    }

    public function testGetAll(): void
    {
        $this->assertSame(
            ['classes', 'exceptions', 'functions', 'interfaces', 'traits'],
            $this->elements->getAll()
        );
    }

    public function testGetEmptyList(): void
    {
        $this->assertSame(
            [
                'classes' => [],
                'exceptions' => [],
                'functions' => [],
                'interfaces' => [],
                'traits' => []
            ],
            $this->elements->getEmptyList()
        );
    }
}

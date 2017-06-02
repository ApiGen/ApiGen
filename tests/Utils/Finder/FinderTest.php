<?php declare(strict_types=1);

namespace ApiGen\Tests\Utils\Finder;

use ApiGen\Utils\Finder\Finder;
use PHPUnit\Framework\TestCase;

final class FinderTest extends TestCase
{
    /**
     * @var Finder
     */
    private $finder;

    protected function setUp(): void
    {
        $this->finder = new Finder;
    }

    public function testSource(): void
    {
        $this->assertCount(1, $this->finder->find([__DIR__ . '/NetteFinderSource']));
        $this->assertCount(3, $this->finder->find([__DIR__ . '/Source']));
        $this->assertCount(1, $this->finder->find([__DIR__ . '/Source/SomeClass.php']));
    }
}

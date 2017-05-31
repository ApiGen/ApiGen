<?php declare(strict_types=1);

namespace ApiGen\Tests\Utils\Finder;

use ApiGen\Utils\Finder\FinderInterface;
use ApiGen\Utils\Finder\NetteFinder;
use PHPUnit\Framework\TestCase;

final class NetteFinderTest extends TestCase
{
    /**
     * @var FinderInterface
     */
    private $finder;

    protected function setUp(): void
    {
        $this->finder = new NetteFinder;
    }

    public function testSource(): void
    {
        $this->assertCount(1, $this->finder->find([__DIR__ . '/NetteFinderSource']));
        $this->assertCount(3, $this->finder->find([__DIR__ . '/Source']));
        $this->assertCount(1, $this->finder->find([__DIR__ . '/Source/SomeClass.php']));
    }
}

<?php declare(strict_types=1);

namespace ApiGen\Utils\Tests\Finder;

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

        $files = $this->finder->find([__DIR__ . '/Source']);
        $this->assertCount(4, $files);

        $files = $this->finder->find([__DIR__ . '/Source'], ['*Another*']);
        $this->assertCount(3, $files);

        $files = $this->finder->find([__DIR__ . '/Source'], [], ['php5']);
        $this->assertCount(1, $files);
    }

    public function testFindSingleFile(): void
    {
        $files = $this->finder->find([__DIR__ . '/Source/SomeClass.php']);
        $this->assertCount(1, $files);
    }

    public function testExclude(): void
    {
        $this->assertCount(0, $this->finder->find([__DIR__ . '/NetteFinderSource'], ['SomeClass.php']));
    }

    public function testExtensions(): void
    {
        $this->assertCount(0, $this->finder->find([__DIR__ . '/NetteFinderSource'], [], ['php5']));
    }

    public function testNoFound(): void
    {
        $this->assertCount(0, $this->finder->find([__DIR__ . '/Source'], [], ['php6']));
    }
}

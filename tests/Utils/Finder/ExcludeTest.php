<?php declare(strict_types=1);

namespace ApiGen\Utils\Tests\Finder;

use ApiGen\Utils\Finder\NetteFinder;
use PHPUnit\Framework\TestCase;

final class ExcludeTest extends TestCase
{
    /**
     * @var NetteFinder
     */
    private $scanner;

    protected function setUp(): void
    {
        $this->scanner = new NetteFinder;
    }

    /**
     * Issue #412
     */
    public function testExcludeAppliedOnlyOnSourcesPath(): void
    {
        $files = $this->scanner->find([__DIR__ . '/Source'], ['tests']);
        $this->assertCount(3, $files);
    }

    /**
     * Issue #529
     */
    public function testExcludeDirRelativeToSource(): void
    {
        $source = [__DIR__ . '/ScannerExcludeSource/src'];
        $this->assertCount(0, $this->scanner->find($source, ['Core/smarty_cache']));
        $this->assertCount(0, $this->scanner->find($source, ['/Core/smarty_cache']));
        $this->assertCount(1, $this->scanner->find($source, ['src/Core/smarty_cache']));
    }

    public function testExcludeFile(): void
    {
        $source = [__DIR__ . '/ScannerExcludeSource/src'];
        $this->assertCount(0, $this->scanner->find($source, ['ShouldBeExcluded.php']));
        $this->assertCount(0, $this->scanner->find($source, ['*ShouldBeExcluded*']));
    }
}

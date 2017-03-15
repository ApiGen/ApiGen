<?php declare(strict_types=1);

namespace ApiGen\Tests\Theme;

use ApiGen\Theme\ThemeResources;
use ApiGen\Utils\FileSystem;
use Mockery;
use PHPUnit\Framework\TestCase;

class ThemeResourcesTest extends TestCase
{

    public function testCopyToDestination(): void
    {
        $sourceDir = TEMP_DIR . '/source';
        $sourceFile = TEMP_DIR . '/other-source/other-file.txt';
        $destinationDir = TEMP_DIR . '/destination';

        $configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
        $configurationMock->shouldReceive('getOption')->with('template')->andReturn([
            'resources' => [
                $sourceFile => 'other-file-renamed.txt',
                $sourceDir => 'assets'
            ]
        ]);
        $this->prepareSources($sourceFile, $sourceDir);

        $themeResources = new ThemeResources($configurationMock, new FileSystem);
        $themeResources->copyToDestination($destinationDir);

        $this->assertFileExists($destinationDir . '/assets/file.txt');
        $this->assertFileExists($destinationDir . '/other-file-renamed.txt');
    }


    private function prepareSources(string $sourceFile, string $sourceDir): void
    {
        mkdir(dirname($sourceFile), 0777);
        file_put_contents($sourceFile, '...');
        mkdir($sourceDir, 0777);
        file_put_contents($sourceDir . '/file.txt', '...');
    }
}

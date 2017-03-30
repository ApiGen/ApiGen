<?php declare(strict_types=1);

namespace ApiGen\Tests\Theme;

use ApiGen\Configuration\Configuration;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Theme\ThemeResources;
use ApiGen\Utils\FileSystem;

final class ThemeResourcesTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp(): void
    {
        $this->configuration = $this->container->getByType(Configuration::class);
    }

    public function testCopyToDestination(): void
    {
        $sourceDir = TEMP_DIR . '/source';
        $sourceFile = TEMP_DIR . '/other-source/other-file.txt';
        $destinationDir = TEMP_DIR . '/destination';

        $this->configuration->resolveOptions([
            'source' => [__DIR__],
            'destination' => __DIR__ . '/Destination',
            'template' => [
                'resources' => [
                    $sourceFile => 'other-file-renamed.txt',
                    $sourceDir => 'assets'
                ]
            ]
        ]);

        $this->prepareSources($sourceFile, $sourceDir);

        $themeResources = new ThemeResources($this->configuration, new FileSystem);
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

<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Utils\FileSystem;
use PHPUnit\Framework\TestCase;

final class RelativePathResolverTest extends TestCase
{

    public function testGetRelativePath(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('getOption')
            ->willReturn([TEMP_DIR]);

        $relativePathResolver = new RelativePathResolver($configuration, new FileSystem);

        $this->assertSame('some-file.txt', $relativePathResolver->getRelativePath(TEMP_DIR . '/some-file.txt'));
        $this->assertSame(
            'some/dir/some-file.txt',
            $relativePathResolver->getRelativePath(TEMP_DIR . '/some/dir/some-file.txt')
        );
    }


    public function testGetRelativePathWithWindowsPath(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('getOption')
            ->willReturn(['C:\some\dir']);

        $relativePathResolver = new RelativePathResolver($configuration, new FileSystem);

        $this->assertSame('file.txt', $relativePathResolver->getRelativePath('C:\some\dir\file.txt'));
        $this->assertSame('more-dir/file.txt', $relativePathResolver->getRelativePath('C:\some\dir\more-dir\file.txt'));
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetRelativePathInvalid(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('getOption')
            ->willReturn([TEMP_DIR]);

        $relativePathResolver = new RelativePathResolver($configuration, new FileSystem);

        $relativePathResolver->getRelativePath('/var/dir/some-strange-file.txt');
    }


    /**
     * Issue #408
     */
    public function testGetRelativePathWithSourceEndingSlash(): void
    {
        $configurationMock = $this->createMock(ConfigurationInterface::class);
        $configurationMock->method('getOption')
            ->with('source')
            ->willReturn(['ProjectBeta']);

        $relativePathResolver = new RelativePathResolver($configurationMock, new FileSystem);

        $fileName = 'ProjectBeta/entities/Category.php';
        $this->assertSame('entities/Category.php', $relativePathResolver->getRelativePath($fileName));
    }
}

<?php declare(strict_types=1);

namespace ApiGen\Tests\Utils;

use ApiGen\Utils\FileSystem;
use PHPUnit\Framework\TestCase;

final class FileSystemCopyTest extends TestCase
{
    public function test(): void
    {
        $fileSystem = new FileSystem;
        $fileSystem->copyDirectory(
            __DIR__ . '/FileSystemCopySource/SomeDir',
            TEMP_DIR . DIRECTORY_SEPARATOR . 'NewDir'
        );

        $this->assertFileExists(TEMP_DIR . DIRECTORY_SEPARATOR .  'NewDir');
    }
}

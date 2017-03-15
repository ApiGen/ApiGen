<?php declare(strict_types=1);

namespace ApiGen\Utils\Tests\Neon;

use ApiGen\Utils\Neon\NeonFile;
use PHPUnit\Framework\TestCase;

final class NeonFileTest extends TestCase
{

    public function testRead(): void
    {
        file_put_contents(TEMP_DIR . '/config.neon', 'var: value');
        $neonFile = new NeonFile(TEMP_DIR . '/config.neon');

        $options = $neonFile->read();
        $this->assertSame(['var' => 'value'], $options);
    }


    /**
     * @expectedException \ApiGen\Utils\Neon\Exceptions\MissingFileException
     */
    public function testCreateNotExisting(): void
    {
        new NeonFile(TEMP_DIR . '/not-here.neon');
    }


    /**
     * @expectedException \ApiGen\Utils\Neon\Exceptions\FileNotReadableException
     */
    public function testFileNotReadable(): void
    {
        $dirPath = TEMP_DIR . '/some-dir';
        mkdir($dirPath, 0777, true);
        $filePath = $dirPath . '/not-readable.neon';
        file_put_contents($filePath, '...');
        exec('chmod 0200 ' . $filePath);

        // strange!

        new NeonFile($filePath);
    }
}

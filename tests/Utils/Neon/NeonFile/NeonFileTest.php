<?php

namespace ApiGen\Utils\Tests\Neon;

use ApiGen\Utils\Neon\NeonFile;
use PHPUnit_Framework_TestCase;

class NeonFileTest extends PHPUnit_Framework_TestCase
{

    public function testRead()
    {
        file_put_contents(TEMP_DIR . '/config.neon', 'var: value');
        $neonFile = new NeonFile(TEMP_DIR . '/config.neon');

        $options = $neonFile->read();
        $this->assertSame(['var' => 'value'], $options);
    }


    public function testCreateNotExisting()
    {
        $this->setExpectedException('ApiGen\Utils\Neon\Exceptions\MissingFileException');
        new NeonFile(TEMP_DIR . '/not-here.neon');
    }


    public function testFileNotReadable()
    {
        $dirPath = TEMP_DIR . '/some-dir';
        mkdir($dirPath, 0777, true);
        $filePath = $dirPath . '/not-readable.neon';
        file_put_contents($filePath, '...');
        exec('chmod 0200 ' . $filePath);

        // strange!

        $this->setExpectedException('ApiGen\Utils\Neon\Exceptions\FileNotReadableException');
        new NeonFile($filePath);
    }
}

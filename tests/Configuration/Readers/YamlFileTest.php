<?php

namespace ApiGen\Tests\Configuration\Readers;

use ApiGen;
use ApiGen\Configuration\Readers\Exceptions\MissingFileException;
use ApiGen\Configuration\Readers\YamlFile;
use PHPUnit_Framework_TestCase;

class YamlFileTest extends PHPUnit_Framework_TestCase
{

    public function testRead()
    {
        file_put_contents(TEMP_DIR . '/config.yaml', 'var: value');
        $yamlFile = new YamlFile(TEMP_DIR . '/config.yaml');

        $options = $yamlFile->read();
        $this->assertSame(['var' => 'value'], $options);
    }


    public function testCreateNotExisting()
    {
        $this->setExpectedException(MissingFileException::class);
        new YamlFile(TEMP_DIR . '/not-here.yaml');
    }
}

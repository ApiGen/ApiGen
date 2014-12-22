<?php

namespace ApiGen\Tests\Neon;

use ApiGen;
use ApiGen\Neon\NeonFile;
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


	/**
	 * @expectedException ApiGen\Neon\Exceptions\MissingFileException
	 */
	public function testCreateNotExisting()
	{
		new NeonFile(TEMP_DIR . '/not-here.neon');
	}

}

<?php

namespace ApiGen\Tests\Charset;

use ApiGen\Charset\CharsetConvertor;
use ApiGen\Charset\CharsetDetector;
use ApiGen\Charset\Encoding;
use Mockery;
use PHPUnit_Framework_TestCase;


class CharsetConvertorTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var CharsetConvertor
	 */
	private $charsetConvertor;


	protected function setUp()
	{
		$charsetDetectorMock = Mockery::mock(CharsetDetector::class);
		$charsetDetectorMock->shouldReceive('detectForFilePath')->andReturn(Encoding::WIN_1250);
		$this->charsetConvertor = new CharsetConvertor($charsetDetectorMock);
	}


	public function testConvertFileToUtf()
	{
		$filePath = __DIR__ . '/files/WINDOWS_1250.php';
		$convertedFileContent = $this->charsetConvertor->convertFileToUtf($filePath);
		$this->assertSame(file_get_contents(__DIR__ . '/files/UTF_8.php'), $convertedFileContent);
	}

}

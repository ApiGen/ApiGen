<?php

namespace ApiGen\Tests\Charset;

use ApiGen\Charset\CharsetDetector;
use ApiGen\Charset\Encoding;
use ApiGen\Tests\ContainerAwareTestCase;
use ReflectionClass;


class CharsetDetectorTest extends ContainerAwareTestCase
{

	/**
	 * @var CharsetDetector
	 */
	private $charsetDetector;


	protected function setUp()
	{
		$this->charsetDetector = $this->container->getByType('ApiGen\Charset\CharsetDetector');
	}


	public function testDetectForFilePath()
	{
		$utf8FilePath = __DIR__ . '/files/UTF_8.php';
		$fileEncoding = $this->detectForFilePath($utf8FilePath);
		$this->assertSame(Encoding::UTF_8, $fileEncoding);

		$windows1250FilePath = __DIR__ . '/files/WINDOWS_1250.php';
		$fileEncoding = $this->detectForFilePath($windows1250FilePath);
		$this->assertSame(Encoding::WIN_1250, $fileEncoding);
	}


	/**
	 * @param string $filePath
	 * @return string
	 */
	private function detectForFilePath($filePath)
	{
		$classReflection = new ReflectionClass($this->charsetDetector);
		$detectFileEncodingMethod = $classReflection->getMethod('detectForFilePath');
		$detectFileEncodingMethod->setAccessible(TRUE);
		return $detectFileEncodingMethod->invokeArgs($this->charsetDetector, [$filePath]);
	}

}

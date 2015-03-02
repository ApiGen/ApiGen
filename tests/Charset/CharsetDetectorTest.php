<?php

namespace ApiGen\Tests\Charset;

use ApiGen\Charset\CharsetDetector;
use ApiGen\Charset\Configuration\CharsetOptionsResolver;
use ApiGen\Charset\Encoding;
use ApiGen\Configuration\OptionsResolverFactory;
use Mockery;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CharsetDetectorTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var CharsetDetector
	 */
	private $charsetDetector;


	protected function setUp()
	{
		$optionsResolverFactoryMock = Mockery::mock(OptionsResolverFactory::class);
		$optionsResolverFactoryMock->shouldReceive('create')->andReturn(new OptionsResolver);
		$charsetOptionsResolver = new CharsetOptionsResolver($optionsResolverFactoryMock);
		$this->charsetDetector = new CharsetDetector($charsetOptionsResolver);
		$this->charsetDetector->setCharsets([Encoding::UTF_8, Encoding::WIN_1250]);
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

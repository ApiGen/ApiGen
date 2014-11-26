<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Charset;

use ApiGen\Charset\CharsetDetector;
use ApiGen\Charset\Encoding;
use Nette\DI\Container;
use ReflectionClass;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


class CharsetDetectorTest extends TestCase
{

	/**
	 * @var Container
	 */
	private $container;

	/**
	 * @var CharsetDetector
	 */
	private $charsetDetector;


	protected function setUp()
	{
		$this->container = createContainer();
		$this->charsetDetector = $this->container->getByType('ApiGen\Charset\CharsetDetector');
	}


	public function testDetectForFilePath()
	{
		$utf8FilePath = __DIR__ . '/files/UTF_8.php';
		$fileEncoding = $this->detectForFilePath($utf8FilePath);
		Assert::same(Encoding::UTF_8, $fileEncoding);

		$windows1250FilePath = __DIR__ . '/files/WINDOWS_1250.php';
		$fileEncoding = $this->detectForFilePath($windows1250FilePath);
		Assert::same(Encoding::WIN_1250, $fileEncoding);
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


(new CharsetDetectorTest)->run();

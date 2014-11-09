<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Charset;

use ApiGen\Charset\CharsetConvertor;
use ApiGen\Charset\Encoding;
use Nette\DI\Container;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


class CharsetConvertorTest extends TestCase
{

	/**
	 * @var Container
	 */
	private $container;

	/**
	 * @var CharsetConvertor
	 */
	private $charsetConvertor;


	protected function setUp()
	{
		$this->container = createContainer();
		$this->charsetConvertor = $this->container->getByType('ApiGen\Charset\CharsetConvertor');
	}


	public function testInstance()
	{
		Assert::type(
			'ApiGen\Charset\CharsetConvertor',
			$this->charsetConvertor
		);
	}


	public function testDetectFileEncoding()
	{
		$utf8FilePath = __DIR__ . '/files/UTF_8.php';
		$fileEncoding = $this->detectFileEncoding($utf8FilePath);
		Assert::same(Encoding::UTF_8, $fileEncoding);

		$windows1250FilePath = __DIR__ . '/files/WINDOWS_1250.php';
		$fileEncoding = $this->detectFileEncoding($windows1250FilePath);
		Assert::same(Encoding::WIN_1250, $fileEncoding);
	}


	public function testConvertFileToUtf()
	{
		$filePath = __DIR__ . '/files/WINDOWS_1250.php';
		$this->charsetConvertor->setCharsets(array(Encoding::WIN_1250));
		$convertedFileContent = $this->charsetConvertor->convertFileToUtf($filePath);

		Assert::match(
			file_get_contents(__DIR__ . '/files/UTF_8.php'),
			$convertedFileContent
		);
	}


	/**
	 * @param string $filePath
	 * @return string
	 */
	private function detectFileEncoding($filePath)
	{
		$detectFileEncodingMethod = $this->charsetConvertor->getReflection()->getMethod('detectFileEncoding');
		$detectFileEncodingMethod->setAccessible(TRUE);
		return $detectFileEncodingMethod->invokeArgs($this->charsetConvertor, array($filePath));
	}

}


\run(new CharsetConvertorTest);

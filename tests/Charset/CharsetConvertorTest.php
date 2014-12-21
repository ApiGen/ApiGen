<?php

namespace ApiGen\Tests\Charset;

use ApiGen\Charset\CharsetConvertor;
use ApiGen\Tests\ContainerAwareTestCase;
use Nette\DI\Container;


class CharsetConvertorTest extends ContainerAwareTestCase
{

	/**
	 * @var CharsetConvertor
	 */
	private $charsetConvertor;


	protected function setUp()
	{
		$this->charsetConvertor = $this->container->getByType('ApiGen\Charset\CharsetConvertor');
	}


	public function testConvertFileToUtf()
	{
		$filePath = __DIR__ . '/files/WINDOWS_1250.php';
		$convertedFileContent = $this->charsetConvertor->convertFileToUtf($filePath);
		$this->assertSame(file_get_contents(__DIR__ . '/files/UTF_8.php'), $convertedFileContent);
	}

}

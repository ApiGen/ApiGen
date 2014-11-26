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


	public function testConvertFileToUtf()
	{
		$filePath = __DIR__ . '/files/WINDOWS_1250.php';
		$convertedFileContent = $this->charsetConvertor->convertFileToUtf($filePath);

		Assert::match(
			file_get_contents(__DIR__ . '/files/UTF_8.php'),
			$convertedFileContent
		);
	}

}


(new CharsetConvertorTest)->run();

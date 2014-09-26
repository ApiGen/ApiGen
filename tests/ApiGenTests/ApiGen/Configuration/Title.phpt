<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


class TitleTest extends TestCase
{

	const TITLE = 'Project API';


	protected function setUp()
	{
		file_put_contents(__DIR__ . '/apigen.neon', '');
	}


	public function testConfig()
	{
		$this->prepareConfig();
		passthru(APIGEN_BIN . ' generate');

		Assert::match(
			'%A%<title>' . self::TITLE . '</title>%A%',
			file_get_contents(API_DIR . '/index.html')
		);
	}


	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR);
		$config['destination'] = API_DIR;
		$config['title'] = self::TITLE;
		$neonFile->write($config);
	}


	protected function tearDown()
	{
		@unlink(__DIR__ . '/apigen.neon');
	}

}


\run(new TitleTest);

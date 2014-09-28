<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class TitleTest extends TestCase
{

	const TITLE = 'Project API';


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

}


\run(new TitleTest);

<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class TitleTest extends TestCase
{

	const TITLE = 'Project API';


	public function testConfig()
	{
		$this->runGenerateCommand('--title=' . self::TITLE);
		Assert::match(
			'%A%<title>' . self::TITLE . '</title>%A%',
			file_get_contents(API_DIR . '/index.html')
		);
	}

}


(new TitleTest)->run();

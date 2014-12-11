<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class ExtensionTest extends TestCase
{

	const PHP5_EXTENSION = 'class-Project.Bar.html';
	const PHP_EXTENSION = 'class-Project.Foo.html';


	public function testConfig()
	{
		$this->runGenerateCommand();
		Assert::false(file_exists(API_DIR . '/' . self::PHP5_EXTENSION));
		Assert::true(file_exists(API_DIR . '/' . self::PHP_EXTENSION));

		$this->runGenerateCommand('--extensions="php5"');
		Assert::true(file_exists(API_DIR . '/' . self::PHP5_EXTENSION));
		Assert::false(file_exists(API_DIR . '/' . self::PHP_EXTENSION));

		$this->runGenerateCommand('--extensions="php"');
		Assert::false(file_exists(API_DIR . '/' . self::PHP5_EXTENSION));
		Assert::true(file_exists(API_DIR . '/' . self::PHP_EXTENSION));

		$this->runGenerateCommand('--extensions="php,php5"');
		Assert::true(file_exists(API_DIR . '/' . self::PHP5_EXTENSION));
		Assert::true(file_exists(API_DIR . '/' . self::PHP_EXTENSION));
	}

}


(new ExtensionTest)->run();

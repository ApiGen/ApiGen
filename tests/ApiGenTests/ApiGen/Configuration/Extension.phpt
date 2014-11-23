<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class ExtensionTest extends TestCase
{

	const PHP5_EXTENSION = 'class-Project.Bar.html';
	const PHP_EXTENSION = 'class-Project.Foo.html';


	public function testConfig()
	{
		$this->prepareConfig();
		passthru(APIGEN_BIN . ' generate');

		Assert::false(file_exists(API_DIR . '/' . self::PHP5_EXTENSION));
		Assert::true(file_exists(API_DIR . '/' . self::PHP_EXTENSION));

		$this->prepareConfig(['php5']);
		passthru(APIGEN_BIN . ' generate');

		Assert::true(file_exists(API_DIR . '/' . self::PHP5_EXTENSION));
		Assert::false(file_exists(API_DIR . '/' . self::PHP_EXTENSION));

		$this->prepareConfig(['php']);
		passthru(APIGEN_BIN . ' generate');

		Assert::false(file_exists(API_DIR . '/' . self::PHP5_EXTENSION));
		Assert::true(file_exists(API_DIR . '/' . self::PHP_EXTENSION));

		$this->prepareConfig(['php', 'php5']);
		passthru(APIGEN_BIN . ' generate');

		Assert::true(file_exists(API_DIR . '/' . self::PHP5_EXTENSION));
		Assert::true(file_exists(API_DIR . '/' . self::PHP_EXTENSION));
	}


	private function prepareConfig(array $extensions = [])
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = [PROJECT_DIR];
		$config['destination'] = API_DIR;
		if ($extensions) {
			$config['extensions'] = $extensions;
		}
		$neonFile->write($config);
	}

}


(new ExtensionTest)->run();

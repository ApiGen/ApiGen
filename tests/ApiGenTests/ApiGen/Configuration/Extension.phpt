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

		Assert::false(file_exists(API_DIR . DS . self::PHP5_EXTENSION));
		Assert::true(file_exists(API_DIR . DS . self::PHP_EXTENSION));

		$this->prepareConfig(array('php5'));
		passthru(APIGEN_BIN . ' generate');

		Assert::true(file_exists(API_DIR . DS . self::PHP5_EXTENSION));
		Assert::false(file_exists(API_DIR . DS . self::PHP_EXTENSION));

		$this->prepareConfig(array('php'));
		passthru(APIGEN_BIN . ' generate');

		Assert::false(file_exists(API_DIR . DS . self::PHP5_EXTENSION));
		Assert::true(file_exists(API_DIR . DS .self::PHP_EXTENSION));

		$this->prepareConfig(array('php', 'php5'));
		passthru(APIGEN_BIN . ' generate');

		Assert::true(file_exists(API_DIR . DS . self::PHP5_EXTENSION));
		Assert::true(file_exists(API_DIR . DS . self::PHP_EXTENSION));
	}


	private function prepareConfig(array $extensions = array())
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR);
		$config['destination'] = API_DIR;
		if ($extensions) {
			$config['extensions'] = $extensions;
		}
		$neonFile->write($config);
	}

}


\run(new ExtensionTest);

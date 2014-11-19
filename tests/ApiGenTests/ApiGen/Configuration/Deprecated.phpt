<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;
use Tester\DomQuery;


require_once __DIR__ . '/../../bootstrap.php';


class DeprecatedTest extends TestCase
{

	public function testConfig()
	{
		$this->prepareConfig();
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/deprecated.html'));

		$this->prepareConfig(FALSE);
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/deprecated.html'));

		$this->prepareConfig(TRUE);
		passthru(APIGEN_BIN . ' generate');
		Assert::true(file_exists(API_DIR . '/deprecated.html'));
	}


	public function testDeprecatedContent()
	{
		$this->prepareConfig(TRUE);
		passthru(APIGEN_BIN . ' generate');
		$content = file_get_contents(API_DIR . '/deprecated.html');

		Assert::match(
			'%A%<span>Deprecated</span>%A%',
			$content
		);

		// class annotation
		Assert::match(
			'%A%<caption>Classes summary</caption>%A%',
			$content
		);
		Assert::match(
			'%A%Project\Deprecated%A%',
			$content
		);

		// method annotation
		Assert::match(
			'%A%<caption>Methods summary</caption>%A%',
			$content
		);
		Assert::match(
			'%A%Project\DeprecatedMethod%A%',
			$content
		);
	}


	public function testDeprecatedClassContent()
	{
		$this->prepareConfig(TRUE);
		passthru(APIGEN_BIN . ' generate');

		// has class="deprecated" attribute
		Assert::match(
			'%A%<h1 class="deprecated">Class Deprecated</h1>%A%',
			file_get_contents(API_DIR . '/class-Project.Deprecated.html')
		);
	}


	public function testDeprecatedMethodContent()
	{
		$this->prepareConfig(TRUE);
		passthru(APIGEN_BIN . ' generate');

		// has class="deprecated" attribute
		Assert::match(
			'%A%id="_getDrink" class="deprecated"%A%',
			file_get_contents(API_DIR . '/class-Project.DeprecatedMethod.html')
		);
	}


	/**
	 * @param bool $deprecatedState
	 */
	private function prepareConfig($deprecatedState = NULL)
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR);
		$config['destination'] = API_DIR;
		if ($deprecatedState !== NULL) {
			$config['deprecated'] = $deprecatedState;
		}
		$neonFile->write($config);
	}

}


\run(new DeprecatedTest);

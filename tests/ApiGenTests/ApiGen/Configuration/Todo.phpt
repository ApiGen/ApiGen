<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class TodoTest extends TestCase
{

	public function testConfig()
	{
		$this->prepareConfig();
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/todo.html'));

		$this->prepareConfig(FALSE);
		passthru(APIGEN_BIN . ' generate');
		Assert::false(file_exists(API_DIR . '/todo.html'));

		$this->prepareConfig(TRUE);
		passthru(APIGEN_BIN . ' generate');
		Assert::true(file_exists(API_DIR . '/todo.html'));
	}


	public function testTodoContent()
	{
		$this->prepareConfig(TRUE);
		passthru(APIGEN_BIN . ' generate');
		$content = file_get_contents(API_DIR . '/todo.html');

		Assert::match(
			'%A%<span>Todo</span>%A%',
			$content
		);

		// class annotation
		Assert::match(
			'%A%<caption>Classes summary</caption>%A%',
			$content
		);
		Assert::match(
			'%A%Project\Todo%A%',
			$content
		);
		Assert::match(
			'%A%This class should be less complex.%A%',
			$content
		);

		// method annotation
		Assert::match(
			'%A%<caption>Methods summary</caption>%A%',
			$content
		);
		Assert::match(
			'%A%Project\TodoMethod%A%',
			$content
		);
		Assert::match(
			'%A%getMates%A%',
			$content
		);
		Assert::match(
			'%A%Update return values to the newest state.%A%',
			$content
		);
	}


	/**
	 * @param bool $todoStatus
	 */
	private function prepareConfig($todoStatus = NULL)
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR);
		$config['destination'] = API_DIR;
		if ($todoStatus !== NULL) {
			$config['todo'] = $todoStatus;
		}
		$neonFile->write($config);
	}

}


\run(new TodoTest);

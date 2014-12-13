<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class TodoTest extends TestCase
{

	public function testConfig()
	{
		$this->runGenerateCommand('--todo');
		Assert::true(file_exists(API_DIR . '/todo.html'));
	}


	public function testTodoContent()
	{
		$this->runGenerateCommand('--todo');
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

}


(new TodoTest)->run();

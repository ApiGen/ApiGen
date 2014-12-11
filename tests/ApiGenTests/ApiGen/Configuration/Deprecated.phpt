<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Configuration;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class DeprecatedTest extends TestCase
{


	public function testConfig()
	{
		$this->runGenerateCommand();
		Assert::false(file_exists(API_DIR . '/deprecated.html'));

		$this->runGenerateCommand('--deprecated');
		Assert::true(file_exists(API_DIR . '/deprecated.html'));
	}


	public function testDeprecatedContent()
	{
		$this->runGenerateCommand('--deprecated');
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
		$this->runGenerateCommand('--deprecated');

		// has class="deprecated" attribute
		Assert::match(
			'%A%<h1 class="deprecated">Class Deprecated</h1>%A%',
			file_get_contents(API_DIR . '/class-Project.Deprecated.html')
		);
	}


	public function testDeprecatedMethodContent()
	{
		$this->runGenerateCommand('--deprecated');

		// has class="deprecated" attribute
		Assert::match(
			'%A%id="_getDrink" class="deprecated"%A%',
			file_get_contents(API_DIR . '/class-Project.DeprecatedMethod.html')
		);
	}

}


(new DeprecatedTest)->run();

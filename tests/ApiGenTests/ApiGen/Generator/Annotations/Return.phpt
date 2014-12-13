<?php

/**
 * @testCase
 * @see Project\Returner
 */

namespace ApiGenTests\ApiGen\Generator\Annotations;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../../bootstrap.php';


class ReturnTest extends TestCase
{

	public function testBasicGeneration()
	{
		$this->runGenerateCommand();
		Assert::true(file_exists(API_DIR . '/index.html'));

		$returnerClass = API_DIR . '/class-Project.Returner.html';
		Assert::true(file_exists($returnerClass));

		$returnerClassContent = $this->getFileContentInOneLine($returnerClass);

		Assert::match(
			'%A%<code>array|<code><a href="class-Project.Foo.html">Project\Foo</a>[]</code></code>%A%',
			$returnerClassContent
		);
		Assert::match(
			'%A%<code>integer</code><br>Number of items.%A%',
			$returnerClassContent
		);
		Assert::match(
			'%A%<code>integer|boolean</code><br>Number of items or FALSE in case of none.%A%',
			$returnerClassContent
		);
	}

}


(new ReturnTest)->run();

<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Generator;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../../bootstrap.php';


class UsesTest extends TestCase
{

	public function testGenerate()
	{
		$this->runGenerateCommand();
		$annotationsFile = API_DIR . '/class-Project.Annotations.html';

		Assert::match(
			'%A%<a href="class-Project.Foo.html#_getName">Project\Foo::getName()</a>%A%',
			file_get_contents($annotationsFile)
		);
	}

}


(new UsesTest)->run();

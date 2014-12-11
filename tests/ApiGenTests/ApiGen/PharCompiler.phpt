<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen;

use ApiGen\PharCompiler;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';


class PharCompilerTest extends TestCase
{

	public function testBasicGeneration()
	{
		$compiler = new PharCompiler(__DIR__ . '/../../..');
		$compiler->compile(TEMP_DIR . '/apigen.phar');
		Assert::true(file_exists(TEMP_DIR . '/apigen.phar'));

		$this->runGenerateCommand(NULL, PROJECT_DIR, 'php ' . TEMP_DIR . '/apigen.phar');
		Assert::true(file_exists(API_DIR . '/index.html'));

		$fooClassFile = API_DIR . '/source-class-Project.Foo.html';
		Assert::true(file_exists($fooClassFile));

		$fooClassFileSource = file_get_contents($fooClassFile);
		Assert::true(strlen($fooClassFileSource) > 1);

		// issue #386
		rename(TEMP_DIR . '/apigen.phar', TEMP_DIR . '/apigen');
		$output = $this->runGenerateCommand(NULL, PROJECT_DIR, 'php ' . TEMP_DIR . '/apigen');
		Assert::notSame([], $output);
	}

}


(new PharCompilerTest)->run();

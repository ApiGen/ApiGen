<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen;

use ApiGen\Neon\NeonFile;
use ApiGen\PharCompiler;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';


class PharCompilerTest extends TestCase
{

	public function testBasicGeneration()
	{
		$this->prepareConfig();

		$compiler = new PharCompiler(__DIR__ . '/../../..');
		$compiler->compile(TEMP_DIR . '/apigen.phar');
		Assert::true(file_exists(TEMP_DIR . '/apigen.phar'));

		passthru('php ' . TEMP_DIR . '/apigen.phar generate');
		Assert::true(file_exists(API_DIR . '/index.html'));

		$fooClassFile = API_DIR . '/source-class-Project.Foo.html';
		Assert::true(file_exists($fooClassFile));

		$fooClassFileSource = file_get_contents($fooClassFile);
		Assert::true(strlen($fooClassFileSource) > 1);

		// issue #386
		rename(TEMP_DIR . '/apigen.phar', TEMP_DIR . '/apigen');
		passthru('php ' . TEMP_DIR . '/apigen', $exitCode);
		Assert::same(0, $exitCode);
	}


	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] =  array(PROJECT_DIR);
		$config['destination'] = API_DIR;
		$neonFile->write($config);
	}

}


\run(new PharCompilerTest);

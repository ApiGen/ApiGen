<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Console;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class IOTest extends TestCase
{

	public function testNotQuiet()
	{
		$this->prepareConfig();
		exec(APIGEN_BIN . ' generate', $output);
		Assert::notSame(array(), $output);
	}


	public function testQuiet()
	{
		exec(APIGEN_BIN . ' generate -q', $output);
		Assert::same([], $output);
	}


	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = [PROJECT_DIR];
		$config['destination'] = API_DIR;
		$neonFile->write($config);
	}

}


(new IOTest)->run();

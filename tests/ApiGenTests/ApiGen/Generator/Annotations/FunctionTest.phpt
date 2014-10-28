<?php
/**
 *  @testCase
 */

namespace ApiGenTests\ApiGen;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../../bootstrap.php';


class FunctionTest extends TestCase
{

	public function testFunction()
	{
		$this->prepareConfig();
		
		passthru(APIGEN_BIN . ' generate');
		Assert::true(file_exists(API_DIR . '/function-ProjectBeta.some_function.html'));
		Assert::true(file_exists(API_DIR . '/source-function-ProjectBeta.some_function.html'));
	}
	
	
	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . DS . 'apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_BETA_DIR);
		$config['destination'] = API_DIR;
		$neonFile->write($config);
	}
}


\run(new FunctionTest);

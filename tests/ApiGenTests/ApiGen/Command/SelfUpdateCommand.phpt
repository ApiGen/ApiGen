<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Command;

use ApiGen\Neon\NeonFile;
use ApiGen\PharCompiler;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class SelfUpdateCommandTest extends TestCase
{

	public function testCommand()
	{
		$this->prepareConfig(array('public'));

		$compiler = new PharCompiler(__DIR__ . '/../../../..');

		$apigenPharFile = TEMP_DIR . '/apigen.phar';
		$compiler->compile($apigenPharFile);
		Assert::true(file_exists($apigenPharFile));

		$generatedFileHash = sha1_file($apigenPharFile);
		passthru($apigenPharFile . ' self-update', $output);
		Assert::same(0, $output);

		$downloadedFileHash = sha1_file($apigenPharFile);
		Assert::notSame($generatedFileHash, $downloadedFileHash);
	}


	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR);
		$config['destination'] = API_DIR;
		$neonFile->write($config);
	}

}


\run(new SelfUpdateCommandTest);

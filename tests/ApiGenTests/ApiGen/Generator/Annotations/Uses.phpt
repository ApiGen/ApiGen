<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Generator;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../../bootstrap.php';


class UsesTest extends TestCase
{

	public function testGenerate()
	{
		$this->prepareConfig();

		passthru(APIGEN_BIN . ' generate', $output);
		$annotationsFile = API_DIR . '/class-Project.Annotations.html';

		Assert::match(
			'%A%<a href="class-Project.Foo.html#_getName">Project\Foo::getName()</a>%A%',
			file_get_contents($annotationsFile)
		);
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


\run(new UsesTest);

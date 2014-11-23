<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Generator;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class TraitMethodsTest extends TestCase
{

	public function testBasicGeneration()
	{
		$this->prepareConfig();

		passthru(APIGEN_BIN . ' generate');
		Assert::true(file_exists(API_DIR . '/index.html'));

		$articleFile = API_DIR . '/class-ProjectBeta.Article.html';
		Assert::true(file_exists($articleFile));

		Assert::match(
			'%A%<a href="class-ProjectBeta.TranslationTrait.html#methods">ProjectBeta\TranslationTrait</a>%A%',
			file_get_contents($articleFile)
		);
		Assert::match(
			'%A%getLang()%A%',
			file_get_contents($articleFile)
		);
		Assert::match(
			'%A%getCode()%A%',
			file_get_contents($articleFile)
		);
		Assert::match(
			'%A%getMagicFoo()%A%',
			file_get_contents($articleFile)
		);
		Assert::match(
			'%A%getMagicMethod()%A%',
			file_get_contents($articleFile)
		);
	}


	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = [PROJECT_BETA_DIR];
		$config['destination'] = API_DIR;
		$neonFile->write($config);
	}

}


(new TraitMethodsTest)->run();

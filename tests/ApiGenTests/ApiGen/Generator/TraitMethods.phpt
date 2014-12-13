<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Generator;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class TraitMethodsTest extends TestCase
{

	public function testBasicGeneration()
	{
		$this->runGenerateCommand(NULL, PROJECT_BETA_DIR);
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

}


(new TraitMethodsTest)->run();

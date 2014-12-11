<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Generator;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class UsingTraitOutsideSourceTest extends TestCase
{

	public function testGenerate()
	{
		$this->runGenerateCommand(NULL, PROJECT_DIR);
		$traitOutsideFile = API_DIR . '/class-Project.TraitOutside.html';
		Assert::true(file_exists($traitOutsideFile));
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.TranslationTrait.html'));

		Assert::match(
			'%A%ProjectBeta\TranslationTrait (not available)%A%',
			file_get_contents($traitOutsideFile)
		);
	}


	public function testGenerateInSource()
	{
		$this->runGenerateCommand(NULL, PROJECT_DIR . ',' . PROJECT_BETA_DIR);
		$traitOutsideFile = API_DIR . '/class-Project.TraitOutside.html';
		Assert::true(file_exists($traitOutsideFile));
		Assert::true(file_exists(API_DIR . '/class-ProjectBeta.TranslationTrait.html'));

		Assert::match(
			'%A%<span>ProjectBeta\TranslationTrait</span>%A% ',
			file_get_contents($traitOutsideFile)
		);
		Assert::match(
			'%A%Methods used from%A%',
			file_get_contents($traitOutsideFile)
		);
		Assert::match(
			'%A%<a href="class-ProjectBeta.TranslationTrait.html#methods">ProjectBeta\TranslationTrait</a>%A%',
			file_get_contents($traitOutsideFile)
		);
	}

}


(new UsingTraitOutsideSourceTest)->run();

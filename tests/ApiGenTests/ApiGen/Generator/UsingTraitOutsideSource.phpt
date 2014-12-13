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
		$this->runGenerateCommand();
		$traitOutsideFile = API_DIR . '/class-Project.TraitOutside.html';
		Assert::true(file_exists($traitOutsideFile));
		Assert::false(file_exists(API_DIR . '/class-ProjectBeta.TranslationTrait.html'));

		Assert::match(
			'%A%ProjectBeta\TranslationTrait (not available)%A%',
			file_get_contents($traitOutsideFile)
		);
	}

}


(new UsingTraitOutsideSourceTest)->run();

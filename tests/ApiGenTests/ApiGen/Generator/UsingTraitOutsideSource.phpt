<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class UsingTraitOutsideSourceTest extends TestCase
{

	public function testGenerate()
	{
		$this->prepareConfig(array(PROJECT_DIR));

		passthru(APIGEN_BIN . ' generate', $output);
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
		$this->prepareConfig(array(PROJECT_DIR, PROJECT_BETA_DIR));

		passthru(APIGEN_BIN . ' generate', $output);
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



	private function prepareConfig(array $source)
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = $source;
		$config['destination'] = API_DIR;
		$neonFile->write($config);
	}

}


\run(new UsingTraitOutsideSourceTest);

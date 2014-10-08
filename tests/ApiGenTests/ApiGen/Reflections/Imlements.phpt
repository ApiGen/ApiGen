<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Reflections;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class ImplementsTests extends TestCase
{

	public function testBasicGeneration()
	{
		$this->prepareConfig();

		passthru(APIGEN_BIN . ' generate');
		Assert::true(file_exists(API_DIR . '/index.html'));

		$implementorClass = API_DIR . '/class-Project.Implementor.html';
		Assert::true(file_exists($implementorClass));

		$implementorClassContent = file_get_contents($implementorClass);
		$implementorClassContentOneLine = preg_replace('/\s+/', ' ', $implementorClassContent);
		$implementorClassContentOneLine = preg_replace('/(?<=>)\s+|\s+(?=<)/', '', $implementorClassContentOneLine);

		// {@inheritDoc} annotation, for php internal interface
		Assert::match(
			'%A%<code>public</code>%A%',
			$implementorClassContentOneLine
		);

		// {@inheritDoc} annotation for our interface
		Assert::match(
			'%A%<code>public array</code>%A%',
			$implementorClassContentOneLine
		);

		// @inheritDoc annotation for our interface
		Assert::match(
			'%A%<code>public integer</code>%A%',
			$implementorClassContentOneLine
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


\run(new ImplementsTests);

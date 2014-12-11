<?php

/**
 * @testCase
 * @see Project\Implementor
 */

namespace ApiGenTests\ApiGen\Reflections;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class ImplementsTest extends TestCase
{

	public function testBasicGeneration()
	{
		$this->runGenerateCommand();
		Assert::true(file_exists(API_DIR . '/index.html'));

		$implementorClass = API_DIR . '/class-Project.Implementor.html';
		Assert::true(file_exists($implementorClass));

		$implementorClassContent = $this->getFileContentInOneLine($implementorClass);

		// {@inheritDoc} annotation, for php internal interface
		Assert::match(
			'%A%<code>public</code>%A%',
			$implementorClassContent
		);

		// {@inheritDoc} annotation for our interface
		Assert::match(
			'%A%<code>public array</code>%A%',
			$implementorClassContent
		);

		// @inheritDoc annotation for our interface
		Assert::match(
			'%A%<code>public integer</code>%A%',
			$implementorClassContent
		);

	}

}


(new ImplementsTest)->run();

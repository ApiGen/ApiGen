<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Generator;

use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../../bootstrap.php';


class FormattingTest extends TestCase
{

	public function testLineParsing()
	{
		$annotationsFileContents = $this->getGeneratedAnnotationsFileContents();
		Assert::contains(
			'<dd>This is description of foo.</dd>',
			$annotationsFileContents
		);
	}


	public function testMarkdownSyntaxApplied()
	{
		$annotationsFileContents = $this->getGeneratedAnnotationsFileContents();
		Assert::contains(
			// note: space before <strong> stripped in getFileContentInOneLine
			'This is description with<strong>bold</strong>.',
			$annotationsFileContents
		);
	}


	public function testMultiLineParamDescription()
	{
		$annotationsFileContents = $this->getGeneratedAnnotationsFileContents();
		Assert::contains(
			'<dd><p>This is simple multi line description, there might be some<code>code</code>.'
            . ' This is second line of simple multi line description.</p></dd>',
			$annotationsFileContents
		);
	}


	/**
	 * @return string
	 */
	private function getGeneratedAnnotationsFileContents()
	{
		$this->runGenerateCommand();
		return $this->getFileContentInOneLine(API_DIR . '/class-Project.Annotations.html');
	}

}


(new FormattingTest)->run();

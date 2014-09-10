<?php

/**
 * TEST: Code blocks highlighting with Markdown
 *
 * @testCase
 */

namespace ApiGenTests\ApiGen;

use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../../bootstrap.php';


class DummyHighlighter implements \ApiGen\Generator\SourceCodeHighlighter
{
	public function highlight($src, $lines = FALSE)
	{
		return "{color}$src{/}";
	}
}


class MarkdownMarkupTest extends TestCase
{

	/** @var \ApiGen\Generator\Markups\MarkdownMarkup */
	private $markup;


	protected function setUp()
	{
		$this->markup = new \ApiGen\Generator\Markups\MarkdownMarkup(
			new \Michelf\MarkdownExtra,
			new DummyHighlighter
		);
	}


	public function testHighlightCodeInBlock()
	{
		$src = file_get_contents(__DIR__ . '/MarkdownMarkup.codeHighlighting.md');
		$html = $this->markup->block($src);
		Assert::matchFile(__DIR__ . '/MarkdownMarkup.codeHighlighting.html', $html);
	}

}


\run(new MarkdownMarkupTest);

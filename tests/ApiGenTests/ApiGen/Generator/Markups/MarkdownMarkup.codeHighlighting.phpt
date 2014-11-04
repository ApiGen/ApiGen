<?php

/**
 * TEST: Code blocks highlighting with Markdown
 *
 * @testCase
 */

namespace ApiGenTests\ApiGen\Generator\Markups;

use ApiGen\Generator\Markups\MarkdownMarkup;
use ApiGen\Generator\Highlighter\SourceCodeHighlighter;
use Michelf\MarkdownExtra;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../../bootstrap.php';


class DummyHighlighter implements SourceCodeHighlighter
{

	public function highlight($src)
	{
		return "{color}$src{/}";
	}


	public function highlightAndAddLineNumbers($src)
	{
	}

}


class MarkdownMarkupTest extends TestCase
{

	/**
	 * @var MarkdownMarkup
	 */
	private $markup;


	protected function setUp()
	{
		$this->markup = new MarkdownMarkup(
			new MarkdownExtra,
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

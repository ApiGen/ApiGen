<?php

namespace ApiGen\Tests\Templating\Filters\Helpers;

use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use Nette\Utils\Html;
use PHPUnit_Framework_TestCase;


class LinkBuilderTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var LinkBuilder
	 */
	private $linkBuilder;


	protected function setUp()
	{
		$this->linkBuilder = new LinkBuilder;
	}


	/**
	 * @dataProvider getBuildData()
	 * @param string $url
	 * @param string $text
	 * @param bool $escape
	 * @param array $classes
	 * @param string $expectedLink
	 */
	public function testBuild($url, $text, $escape, array $classes, $expectedLink)
	{
		$this->assertSame($expectedLink, $this->linkBuilder->build($url, $text, $escape, $classes));
	}


	/**
	 * @return array[]
	 */
	public function getBuildData()
	{
		return [
			['url', 'text', TRUE, [], '<a href="url">text</a>'],
			['url', 'text', FALSE, ['class'], '<a href="url" class="class">text</a>'],
			['url', Html::el('b')->setText('text'), FALSE, [], '<a href="url"><b>text</b></a>'],
			['url', Html::el('b')->setText('text'), TRUE, [], '<a href="url"><b>text</b></a>'],
			['url', '<b>text</b>', TRUE, [], '<a href="url">&lt;b&gt;text&lt;/b&gt;</a>'],
			['url', '<b>text</b>', FALSE, [], '<a href="url"><b>text</b></a>'],
		];
	}

}

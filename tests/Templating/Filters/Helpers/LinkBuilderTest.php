<?php

namespace ApiGen\Tests\Templating\Filters\Helpers;

use ApiGen\Templating\Filters\Helpers\LinkBuilder;
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


	public function testBuild()
	{
		$this->assertSame(
			'<a href="url">text</a>',
			$this->linkBuilder->build('url', 'text')
		);

		$this->assertSame(
			'<a href="url" class="class">text</a>',
			$this->linkBuilder->build('url', 'text', FALSE, ['class'])
		);
	}

}

<?php

namespace ApiGen\Tests\Templating;

use ApiGen\Templating\Template;
use Mockery;
use PHPUnit_Framework_TestCase;


class TemplateTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var Template
	 */
	private $template;


	protected function setUp()
	{
		$latteEngineMock = Mockery::mock('Latte\Engine');
		$latteEngineMock->shouldReceive('render')->andReturn('...');
		$latteEngineMock->shouldReceive('invokeFilter')->andReturnUsing(function ($args) {
			return $args . ' was called';
		});
		$this->template = new Template($latteEngineMock);
	}


	public function testSave()
	{
		$this->template->save(TEMP_DIR . '/dir/file.txt');
		$this->assertFileExists(TEMP_DIR . '/dir/file.txt');
	}


	public function testCallFilter()
	{
		$this->assertSame(
			'namespaceUrl was called',
			$this->template->namespaceUrl('MyNamcespace')
		);
	}


	public function testCallNonFilter()
	{
		$this->assertSame(
			'urlize was called',
			$this->template->urlize('My name')
		);
	}

}

<?php

namespace ApiGen\Tests\Templating;

use ApiGen\Templating\Template;
use Latte\Engine;
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
		$latteEngineMock = Mockery::mock(Engine::class);
		$latteEngineMock->shouldReceive('render')->andReturn('...');
		$this->template = new Template($latteEngineMock);
	}


	public function testSave()
	{
		$this->template->save(TEMP_DIR . '/dir/file.txt');
		$this->assertFileExists(TEMP_DIR . '/dir/file.txt');
	}

}

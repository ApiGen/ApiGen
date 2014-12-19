<?php

namespace ApiGen\Tests\ApiGen\Generator\TemplateGenerators;

use ApiGen\Generator\TemplateGenerators\OverviewGenerator;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;
use Mockery;
use PHPUnit_Framework_TestCase;


class OverviewGeneratorTest extends PHPUnit_Framework_TestCase
{

	public function testGenerate()
	{
		$templateFactoryMock = $this->getTemplateFactoryMock();
		$overviewGenerator = new OverviewGenerator($templateFactoryMock);
		$overviewGenerator->generate();
		$this->assertFileExists(TEMP_DIR . '/index.html');
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getTemplateFactoryMock()
	{
		$templateFactoryMock = Mockery::mock('ApiGen\Templating\TemplateFactory');
		$templateFactoryMock->shouldReceive('createForType')->andReturn($this->getTemplateMock());
		$templateFactoryMock->shouldReceive('save');
		return $templateFactoryMock;
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getTemplateMock()
	{
		$templateMock = Mockery::mock('ApiGen\Templating\Template');
		$templateMock->shouldReceive('setSavePath')->withAnyArgs();
		$templateMock->shouldReceive('save')->andReturn(file_put_contents(TEMP_DIR . '/index.html', '...'));
		return $templateMock;
	}

}

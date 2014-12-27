<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Templating\Filters\UrlFilters;
use Mockery;
use PHPUnit_Framework_TestCase;


class UrlFiltersTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var UrlFilters
	 */
	private $urlFilters;


	protected function setUp()
	{
		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
		$configurationMock->shouldReceive('getOption')->with(CO::TEMPLATE)->andReturn([
			'templates' => [
				'class' => ['filename' => 'class-%s'],
				'constant' => ['filename' => 'constant-%s'],
				'function' => ['filename' => 'function-%s']
			]
		]);
		$markupMock = Mockery::mock('ApiGen\Generator\Markups\Markup');
		$markupMock->shouldReceive('block')->andReturnUsing(function ($arg) {
			return 'Markupped: ' . $arg;
		});

		$sourceCodeHighlighterMock = Mockery::mock('ApiGen\Generator\SourceCodeHighlighter\SourceCodeHighlighter');
		$elementResolverMock = Mockery::mock('ApiGen\Generator\Resolvers\ElementResolver');
		$linkBuilderMock = Mockery::mock('ApiGen\Templating\Filters\Helpers\LinkBuilder');
		$elementUrlFactoryMock = Mockery::mock('ApiGen\Templating\Filters\Helpers\ElementUrlFactory');
		$this->urlFilters = new UrlFilters(
			$configurationMock, $sourceCodeHighlighterMock, $markupMock, $elementResolverMock, $linkBuilderMock,
			$elementUrlFactoryMock
		);
	}


	public function testDoc()
	{
		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$this->assertSame('Markupped: ...', $this->urlFilters->doc('...', $reflectionClassMock, TRUE));
	}

}

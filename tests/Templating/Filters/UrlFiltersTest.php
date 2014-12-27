<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionProperty;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use ApiGen\Templating\Filters\UrlFilters;
use ApiGen\Tests\MethodInvoker;
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
		$markupMock = $this->getMarkupMock();
		$sourceCodeHighlighterMock = Mockery::mock('ApiGen\Generator\SourceCodeHighlighter\SourceCodeHighlighter');
		$elementResolverMock = $this->getElementResolverMock();

		$elementLinkFactoryMock = Mockery::mock('ApiGen\Templating\Filters\Helpers\ElementLinkFactory');
		$elementLinkFactoryMock->shouldReceive('createForElement')->andReturnUsing(
			function (ReflectionClass $reflectionClass) {
				$name = $reflectionClass->getName();
				return '<a href="class-link-' . $name . '">' . $name . '</a>';
			}
		);

		$this->urlFilters = new UrlFilters(
			$this->getConfigurationMock(), $sourceCodeHighlighterMock, $markupMock, $elementResolverMock,
			new LinkBuilder, $elementLinkFactoryMock
		);
	}


	public function testDoc()
	{
		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$this->assertSame('Markupped: ...', $this->urlFilters->doc('...', $reflectionClassMock, TRUE));
		$this->assertSame('Markupped line: ...', $this->urlFilters->doc('...', $reflectionClassMock));
	}


	/**
	 * @dataProvider getInternalData()
	 */
	public function testResolveInternal($docBlock, $expectedLink)
	{
		$this->assertSame(
			$expectedLink,
			MethodInvoker::callMethodOnObject($this->urlFilters, 'resolveInternalAnnotation', [$docBlock])
		);
	}


	public function getInternalData()
	{
		return [
			['{@internal Inside {@link some comment}, foo}', 'Inside {@link some comment}, foo'],
			['{@internal}', ''],
			['{@inherited bar}', '{@inherited bar}'],
		];
	}


	/**
	 * @dataProvider getLinkAndSeeData()
	 */
	public function testResolveLinkAndSeeAnnotation($docBlock, $expectedLink)
	{
		$reflectionElementMock = Mockery::mock('ApiGen\Reflection\ReflectionElement');
		$this->assertSame(
			$expectedLink,
			MethodInvoker::callMethodOnObject($this->urlFilters, 'resolveLinkAndSeeAnnotation', [
				$docBlock, $reflectionElementMock
			])
		);
	}


	/**
	 * @return array[]
	 */
	public function getLinkAndSeeData()
	{
		return [
			['{@link http://apigen.org Description}', '<a href="http://apigen.org">Description</a>'],
			['{@link http://apigen.org}', '<a href="http://apigen.org">http://apigen.org</a>'],
			['{@see http://php.net/manual/en PHP Manual}', '<a href="http://php.net/manual/en">PHP Manual</a>'],
			['{@see ApiGen\ApiGen}', '<code><a href="class-link-ApiGen\ApiGen">ApiGen\ApiGen</a></code>'],
			['{@see NotActiveClass}', 'NotActiveClass']
		];
	}


	public function testDescription()
	{
		$docBlock = <<<DOC
/**
 * Some annotation
 * with more rows
 */
DOC;

		$reflectionElementMock = Mockery::mock('ApiGen\Reflection\ReflectionElement');
		$expected = <<<EXP
Markupped line: * Some annotation
 * with more rows
 */
EXP;
		$this->assertSame($expected, $this->urlFilters->description($docBlock, $reflectionElementMock));
	}


	public function testShortDescription()
	{
		$reflectionElementMock = Mockery::mock('ApiGen\Reflection\ReflectionElement');
		$reflectionElementMock->shouldReceive('getShortDescription')->andReturn('Some short description');

		$this->assertSame(
			'Markupped line: Some short description',
			$this->urlFilters->shortDescription($reflectionElementMock)
		);

		$this->assertSame(
			'Markupped: Some short description',
			$this->urlFilters->shortDescription($reflectionElementMock, TRUE)
		);
	}


	public function testLongDescription()
	{
		$longDescription = <<<DOC
Some long description with example:
<code>echo "hi";</code>
DOC;
		$reflectionElementMock = Mockery::mock('ApiGen\Reflection\ReflectionElement');
		$reflectionElementMock->shouldReceive('getLongDescription')->andReturn($longDescription);

		$expected = <<<EXPECTED
Markupped: Some long description with example:
<code>echo "hi";</code>
EXPECTED;
		$this->assertSame($expected, $this->urlFilters->longDescription($reflectionElementMock));
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getElementResolverMock()
	{
		$elementResolverMock = Mockery::mock('ApiGen\Generator\Resolvers\ElementResolver');
		$elementResolverMock->shouldReceive('resolveElement')->andReturnUsing(function ($arg) {
			if ($arg === 'ApiGen\\ApiGen') {
				$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
				$reflectionClassMock->shouldReceive('getName')->andReturn('ApiGen\\ApiGen');
				$reflectionClassMock->shouldReceive('isDeprecated')->andReturn(TRUE);
				$reflectionClassMock->shouldReceive('isValid')->andReturn(TRUE);
				return $reflectionClassMock;

			} else {
				return NULL;
			}
		});
		return $elementResolverMock;
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getMarkupMock()
	{
		$markupMock = Mockery::mock('ApiGen\Generator\Markups\Markup');
		$markupMock->shouldReceive('block')->andReturnUsing(function ($arg) {
			return 'Markupped: ' . $arg;
		});
		$markupMock->shouldReceive('line')->andReturnUsing(function ($arg) {
			return 'Markupped line: ' . $arg;
		});
		return $markupMock;
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getConfigurationMock()
	{
		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
		$configurationMock->shouldReceive('getOption')->with(CO::INTERNAL)->andReturn(TRUE);
		return $configurationMock;
	}

}

<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Generator\SourceCodeHighlighter\SourceCodeHighlighter;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Templating\Filters\Helpers\ElementLinkFactory;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use ApiGen\Templating\Filters\UrlFilters;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use PHPUnit_Framework_TestCase;


class UrlFiltersTest extends PHPUnit_Framework_TestCase
{

	const APIGEN_LINK = '<code><a href="class-link-ApiGen\ApiGen" class="deprecated invalid">ApiGen\ApiGen</a></code>';

	/**
	 * @var UrlFilters
	 */
	private $urlFilters;


	protected function setUp()
	{
		$markupMock = $this->getMarkupMock();
		$sourceCodeHighlighterMock = Mockery::mock(SourceCodeHighlighter::class);
		$sourceCodeHighlighterMock->shouldReceive('highlight')->andReturnUsing(function ($arg) {
			return 'Highlighted: ' . $arg;
		});
		$elementResolverMock = $this->getElementResolverMock();

		$elementLinkFactoryMock = Mockery::mock(ElementLinkFactory::class);
		$elementLinkFactoryMock->shouldReceive('createForElement')->andReturnUsing(
			function (ReflectionClass $reflectionClass, $classes = '') {
				$name = $reflectionClass->getName();
				if ($classes) {
					$classes = ' class="' . implode($classes, ' ') . '"';
				}
				return '<a href="class-link-' . $name . '"' . $classes . '>' . $name . '</a>';
			}
		);

		$this->urlFilters = new UrlFilters(
			$this->getConfigurationMock(), $sourceCodeHighlighterMock, $markupMock, $elementResolverMock,
			new LinkBuilder, $elementLinkFactoryMock
		);
	}


	public function testDoc()
	{
		$reflectionClassMock = Mockery::mock(ReflectionClass::class);
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
		$reflectionElementMock = Mockery::mock(ReflectionElement::class);
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
			['{@see NotActiveClass}', 'NotActiveClass'],
			[
				'{@see ApiGen\ApiGen}',
				self::APIGEN_LINK
			]
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


	public function testHighlightPhp()
	{
		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$this->assertSame(
			'Highlighted: ...',
			$this->urlFilters->highlightPhp('...', $reflectionClassMock)
		);
	}


	public function testHighlightValue()
	{
		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$this->assertSame(
			'Highlighted: ...',
			$this->urlFilters->highlightValue('...', $reflectionClassMock)
		);
	}


	/**
	 * @dataProvider getTypeLinksData()
	 */
	public function testTypeLinks($annotation, $expected)
	{
		$reflectionClass = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$this->assertSame($expected, $this->urlFilters->typeLinks($annotation, $reflectionClass));
	}


	/**
	 * @return array[]
	 */
	public function getTypeLinksData()
	{
		return [
			['int|string', 'integer|string'],
			['string|$this', 'string|$this'],
			['$this', ''],
			[
				'ApiGen\ApiGen',
				self::APIGEN_LINK
			],
			[
				'ApiGen\ApiGen|string',
				'<code><a href="class-link-ApiGen\ApiGen" class="deprecated invalid">ApiGen\ApiGen</a></code>|string'
			]
		];
	}


	/**
	 * @dataProvider getResolveLinksData()
	 */
	public function testResolveLink($definition, $expected)
	{
		$reflectionClass = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$this->assertSame($expected, $this->urlFilters->resolveLink($definition, $reflectionClass));
	}


	/**
	 * @return array[]
	 */
	public function getResolveLinksData()
	{
		return [
			['int', NULL],
			[
				'ApiGen\ApiGen[]',
				'<code><a href="class-link-ApiGen\ApiGen" class="deprecated invalid">ApiGen\ApiGen</a>[]</code>'
			]
		];
	}


	/**
	 * @dataProvider getAnnotationData()
	 */
	public function testAnnotation($annotation, $name, $expected)
	{
		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$this->assertSame($expected, $this->urlFilters->annotation($annotation, $name, $reflectionClassMock));
	}


	/**
	 * @return array[]
	 */
	public function getAnnotationData()
	{
		return [
			['ApiGen\ApiGen', 'return', self::APIGEN_LINK],
			['ApiGen\ApiGen special class', 'return', self::APIGEN_LINK . '<br>Markupped line: special class'],
			['ApiGen\ApiGen', 'throws', self::APIGEN_LINK],
			['...', 'return', '...'],
			['http://licence.com MIT', 'license', '<a href="http://licence.com">MIT</a>'],
			['http://licence.com MIT', 'link', '<a href="http://licence.com">MIT</a>'],
			['ApiGen\ApiGen', 'link', 'Markupped line: ApiGen\ApiGen'],
			['ApiGen\ApiGen', 'see', self::APIGEN_LINK],
			['ApiGen\ApiGen', 'uses', self::APIGEN_LINK],
			['ApiGen\ApiGen', 'usedby', self::APIGEN_LINK]
		];
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getElementResolverMock()
	{
		$elementResolverMock = Mockery::mock('ApiGen\Generator\Resolvers\ElementResolver');
		$elementResolverMock->shouldReceive('resolveElement')->andReturnUsing(function ($arg) {
			if ($arg === 'ApiGen\ApiGen') {
				$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
				$reflectionClassMock->shouldReceive('getName')->andReturn('ApiGen\ApiGen');
				$reflectionClassMock->shouldReceive('isDeprecated')->andReturn(TRUE);
				$reflectionClassMock->shouldReceive('isValid')->andReturn(FALSE);
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

<?php

namespace ApiGen\Tests\Reflection;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use Nette\Neon\Exception;
use PHPUnit_Framework_TestCase;
use TokenReflection\Broker;
use TokenReflection\Exception\FileProcessingException;


class ReflectionElementTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ReflectionElement
	 */
	private $reflectionClass;


	protected function setUp()
	{
		$backend = new Backend($this->getReflectionFactory());
		$broker = new Broker($backend);
		$broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

		$this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];
	}


	public function testGetExtension()
	{
		$this->assertNull($this->reflectionClass->getExtension());
	}


	public function testGetExtensionName()
	{
		$this->assertFalse($this->reflectionClass->getExtensionName());
	}


	public function testGetStartPosition()
	{
		$this->assertSame(9, $this->reflectionClass->getStartPosition());
	}


	public function testGetEndPosition()
	{
		$this->assertSame(62, $this->reflectionClass->getEndPosition());
	}


	public function testIsMain()
	{
		$this->assertTrue($this->reflectionClass->isMain());
	}


	public function testIsDocumented()
	{
		$this->assertTrue($this->reflectionClass->isDocumented());
	}


	public function testIsDeprecated()
	{
		$this->assertFalse($this->reflectionClass->isDeprecated());
	}


	public function testGetPackageName()
	{
		$this->assertSame('Some\Package', $this->reflectionClass->getPackageName());
	}


	public function testGetPseudoPackageName()
	{
		$this->assertSame('Some\Package', $this->reflectionClass->getPseudoPackageName());
	}


	public function testInPackage()
	{
		$this->assertTrue($this->reflectionClass->inPackage());
	}


	public function testGetNamespaceName()
	{
		$this->assertSame('Project', $this->reflectionClass->getNamespaceName());
	}


	public function testGetPseudoNamespaceName()
	{
		$this->assertSame('Project', $this->reflectionClass->getPseudoNamespaceName());
	}


	public function testInNamespace()
	{
		$this->assertTrue($this->reflectionClass->inNamespace());
	}


	public function testGetNamespacesAliases()
	{
		$this->assertSame([], $this->reflectionClass->getNamespaceAliases());
	}


	public function testGetShortDescription()
	{
		$this->assertSame('This is some description', $this->reflectionClass->getShortDescription());
	}


	public function testGetLongDescription()
	{
		$this->assertSame('This is some description', $this->reflectionClass->getLongDescription());
	}


	public function testGetDocComment()
	{
		$docCommentParts = [];
		$docCommentParts[] = ' * This is some description';
		$docCommentParts[] = ' * @property-read int $skillCounter';
		$docCommentParts[] = ' * @method string getName() This is some short description.';
		$docCommentParts[] = ' * @method string doAnOperation(\stdClass $data, $type) This also some description.';
		$docCommentParts[] = ' * @package Some_Package';

		foreach ($docCommentParts as $part) {
			$this->assertContains($part, $this->reflectionClass->getDocComment());
		}
	}


	public function testGetAnnotations()
	{
		$annotations = $this->reflectionClass->getAnnotations();
		$this->assertCount(3, $annotations);
		$this->assertArrayHasKey('property-read', $annotations);
		$this->assertArrayHasKey('method', $annotations);
		$this->assertArrayHasKey('package', $annotations);
	}


	public function testGetAnnotation()
	{
		$this->assertSame(['Some_Package'], $this->reflectionClass->getAnnotation('package'));
	}


	public function testHasAnnotation()
	{
		$this->assertTrue($this->reflectionClass->hasAnnotation('package'));
		$this->assertFalse($this->reflectionClass->hasAnnotation('nope'));
	}


	public function testAddReason()
	{
		$this->reflectionClass->addReason(new FileProcessingException(['...']));
		$this->assertCount(1, $this->reflectionClass->getReasons());
	}


	public function testGetReasons()
	{
		$this->assertSame([], $this->reflectionClass->getReasons());
	}


	public function testHasReasons()
	{
		$this->assertFalse($this->reflectionClass->hasReasons());
	}


	public function testAddAnnotation()
	{
		$this->assertFalse($this->reflectionClass->hasAnnotation('Foo'));
		$this->reflectionClass->addAnnotation('Foo', '...');
		$this->assertTrue($this->reflectionClass->hasAnnotation('Foo'));
	}


	public function testGetAnnotationFromReflection()
	{
		$annotations = MethodInvoker::callMethodOnObject(
			$this->reflectionClass, 'getAnnotationsFromReflection', [$this->reflectionClass]
		);
		$this->assertSame([], $annotations);
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getReflectionFactory()
	{
		$parserResultMock = Mockery::mock('ApiGen\Parser\ParserResult');
		$parserResultMock->shouldReceive('getElementsByType')->andReturn(['...']);
		return new ReflectionFactory($this->getConfigurationMock(), $parserResultMock);
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getConfigurationMock()
	{
		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
		$configurationMock->shouldReceive('getOption')->with('php')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('deprecated')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('internal')->andReturn(FALSE);
		$configurationMock->shouldReceive('getOption')->with('skipDocPath')->andReturn(['*SomeConstant.php*']);
		$configurationMock->shouldReceive('getOption')->with('main')->andReturn('');
		$configurationMock->shouldReceive('getOption')->with(CO::VISIBILITY_LEVELS)->andReturn(256);
		return $configurationMock;
	}

}

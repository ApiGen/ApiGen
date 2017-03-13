<?php

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Parser\Tests\Configuration\ParserConfiguration;
use ApiGen\Parser\Tests\MethodInvoker;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TokenReflection\Broker;
use TokenReflection\Exception\FileProcessingException;

class ReflectionElementTest extends TestCase
{

    /**
     * @var ElementReflectionInterface
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
            $this->reflectionClass,
            'getAnnotationsFromReflection',
            [$this->reflectionClass]
        );
        $this->assertSame([], $annotations);
    }


    /**
     * @return Mockery\MockInterface|ParserStorageInterface
     */
    private function getReflectionFactory()
    {
        $parserStorageMock = Mockery::mock(ParserStorageInterface::class);
        $parserStorageMock->shouldReceive('getElementsByType')->andReturn(['...']);

        $configurationMock = Mockery::mock(ConfigurationInterface::class, [
            'getVisibilityLevel' => ReflectionProperty::IS_PUBLIC,
            'isInternalDocumented' => false,
            'getMain' => ''
        ]);
        return new ReflectionFactory($configurationMock, $parserStorageMock);
    }
}

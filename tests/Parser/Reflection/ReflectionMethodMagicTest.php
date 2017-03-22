<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionMethodMagic;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TokenReflection\Broker;

final class ReflectionMethodMagicTest extends TestCase
{
    /**
     * @var ReflectionMethodMagic
     */
    private $reflectionMethodMagic;

    /**
     * @var ReflectionClass
     */
    private $reflectionClass;


    protected function setUp(): void
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

        $this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];
        $this->reflectionMethodMagic = $this->reflectionClass->getMagicMethods()['getName'];
    }


    public function testInstance(): void
    {
        $this->assertInstanceOf(MagicMethodReflectionInterface::class, $this->reflectionMethodMagic);
    }


    public function testGetDeclaringClass(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionMethodMagic->getDeclaringClass());
    }


    public function testGetDeclaringClassName(): void
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionMethodMagic->getDeclaringClassName());
    }


    public function testGetName(): void
    {
        $this->assertSame('getName', $this->reflectionMethodMagic->getName());
    }


    public function testGetShortDescription(): void
    {
        $this->assertSame('This is some short description.', $this->reflectionMethodMagic->getShortDescription());
    }


    public function testGetLongDescription(): void
    {
        $this->assertSame('This is some short description.', $this->reflectionMethodMagic->getLongDescription());
    }


    public function testReturnReference(): void
    {
        $this->assertFalse($this->reflectionMethodMagic->returnsReference());
    }


    public function testIsMagic(): void
    {
        $this->assertTrue($this->reflectionMethodMagic->isMagic());
    }


    public function testIsDocumented(): void
    {
        $this->assertTrue($this->reflectionMethodMagic->isDocumented());
    }


    public function testIsDeprecated(): void
    {
        $this->assertFalse($this->reflectionMethodMagic->isDeprecated());
    }


    public function testGetNamespaceName(): void
    {
        $this->assertSame('Project', $this->reflectionMethodMagic->getNamespaceName());
    }


    public function testGetAnnotations(): void
    {
        $this->assertSame(['return' => ['string']], $this->reflectionMethodMagic->getAnnotations());
    }


    public function testIsAbstract(): void
    {
        $this->assertFalse($this->reflectionMethodMagic->isAbstract());
    }


    public function testIsFinal(): void
    {
        $this->assertFalse($this->reflectionMethodMagic->isFinal());
    }


    public function testIsPrivate(): void
    {
        $this->assertFalse($this->reflectionMethodMagic->isPrivate());
    }


    public function testIsProtected(): void
    {
        $this->assertFalse($this->reflectionMethodMagic->isProtected());
    }


    public function testIsPublic(): void
    {
        $this->assertTrue($this->reflectionMethodMagic->isPublic());
    }


    public function testIsStatic(): void
    {
        $this->assertFalse($this->reflectionMethodMagic->isStatic());
    }


    public function testGetDeclaringTrait(): void
    {
        $this->assertNull($this->reflectionMethodMagic->getDeclaringTrait());
    }


    public function testGetDeclaringTraitName(): void
    {
        $this->assertSame('', $this->reflectionMethodMagic->getDeclaringTraitName());
    }


    public function testGetOriginalName(): void
    {
        $this->assertSame('getName', $this->reflectionMethodMagic->getOriginalName());
    }


    public function testStaticMethod(): void
    {
        $method = $this->reflectionClass->getMagicMethods()['doAStaticOperation'];
        $this->assertTrue($method->isStatic());
    }


    public function testStaticMethodReturnType(): void
    {
        $method = $this->reflectionClass->getMagicMethods()['doAStaticOperation'];
        $this->assertSame('string', current($method->getAnnotation('return')));
    }


    public function testVoidStaticMethod(): void
    {
        $method = $this->reflectionClass->getMagicMethods()['doAVoidStaticOperation'];
        $this->assertEmpty(current($method->getAnnotation('return')));
    }


    /**
     * @return ReflectionFactoryInterface|ParserStorageInterface
     */
    private function getReflectionFactory()
    {
        $parserStorageMock = $this->createMock(ParserStorageInterface::class);
        $parserStorageMock->method('getElementsByType')
            ->willReturnCallback(function ($arg) {
                if ($arg) {
                    return ['Project\ReflectionMethod' => $this->reflectionClass];
                }
            });

        $configurationMock = $this->createMock(ConfigurationInterface::class);
        $configurationMock->method('getVisibilityLevel')
            ->willReturn(ReflectionProperty::IS_PUBLIC);
        $configurationMock->method('isInternalDocumented')
            ->willReturn(false);

        return new ReflectionFactory($configurationMock, $parserStorageMock);
    }
}

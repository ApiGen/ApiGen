<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TokenReflection\Broker;

final class ReflectionPropertyMagicTest extends TestCase
{
    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;

    /**
     * @var MagicPropertyReflectionInterface
     */
    private $reflectionPropertyMagic;


    protected function setUp(): void
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

        $this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];

        $this->reflectionPropertyMagic = $this->reflectionClass->getMagicProperties()['skillCounter'];
    }


    public function testInstance(): void
    {
        $this->assertInstanceOf(MagicPropertyReflectionInterface::class, $this->reflectionPropertyMagic);
    }


    public function testIsReadOnly(): void
    {
        $this->assertTrue($this->reflectionPropertyMagic->isReadOnly());
    }


    public function testIsWriteOnly(): void
    {
        $this->assertFalse($this->reflectionPropertyMagic->isWriteOnly());
    }


    public function testIsMagic(): void
    {
        $this->assertTrue($this->reflectionPropertyMagic->isMagic());
    }


    public function testGetTypeHint(): void
    {
        $this->assertSame('int', $this->reflectionPropertyMagic->getTypeHint());
    }


    public function testGetDeclaringClass(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionPropertyMagic->getDeclaringClass());
    }


    public function testGetDeclaringClassName(): void
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionPropertyMagic->getDeclaringClassName());
    }


    public function testGetDefaultValue(): void
    {
        $this->assertNull($this->reflectionPropertyMagic->getDefaultValue());
    }


    public function testIsDefault(): void
    {
        $this->assertFalse($this->reflectionPropertyMagic->isDefault());
    }


    public function testIsPrivate(): void
    {
        $this->assertFalse($this->reflectionPropertyMagic->isPrivate());
    }


    public function testIsProtected(): void
    {
        $this->assertFalse($this->reflectionPropertyMagic->isProtected());
    }


    public function testIsPublic(): void
    {
        $this->assertTrue($this->reflectionPropertyMagic->isPublic());
    }


    public function testIsStatic(): void
    {
        $this->assertFalse($this->reflectionPropertyMagic->isStatic());
    }


    public function testGetDeclaringTrait(): void
    {
        $this->assertNull($this->reflectionPropertyMagic->getDeclaringTrait());
    }


    public function testGetDeclaringTraitName(): void
    {
        $this->assertSame('', $this->reflectionPropertyMagic->getDeclaringTraitName());
    }


    /**
     * @return ReflectionFactoryInterface
     */
    private function getReflectionFactory()
    {
        $parserStorageMock = $this->createMock(ParserStorageInterface::class);
        $parserStorageMock->method('getElementsByType')->willReturnUsing(function ($arg) {
            if ($arg) {
                return ['Project\ReflectionMethod' => $this->reflectionClass];
            }
        });
        $configurationMock = $this->createMock(ConfigurationInterface::class, [
            'getVisibilityLevel' => ReflectionProperty::IS_PUBLIC,
            'isInternalDocumented' => false,
        ]);

        return new ReflectionFactory($configurationMock, $parserStorageMock);
    }
}

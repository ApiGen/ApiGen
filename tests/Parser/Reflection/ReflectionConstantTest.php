<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionBase;
use ApiGen\Tests\ConstantInClass;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use TokenReflection\Broker;

final class ReflectionConstantTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ConstantReflectionInterface
     */
    private $constantReflection;

    /**
     * @var ConstantReflectionInterface|ReflectionBase
     */
    private $constantReflectionInClass;

    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;

    protected function setUp(): void
    {
        $broker = $this->container->getByType(Broker::class);
        $broker->processDirectory(__DIR__ . '/ReflectionConstantSource');

        $backend = $this->container->getByType(Backend::class);
        $this->constantReflection = $backend->getConstants()['SOME_CONSTANT'];
        $this->reflectionClass = $backend->getClasses()[ConstantInClass::class];

        $this->constantReflectionInClass = $this->reflectionClass->getConstant('CONSTANT_INSIDE');

        /** @var ParserStorageInterface $parserStorage */
        $parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $parserStorage->setClasses([
            ConstantInClass::class => $this->reflectionClass
        ]);
        $parserStorage->setConstants([
            'SOME_CONSTANT' => $this->constantReflection
        ]);

        $this->constantReflectionInClass->setParserStorage($parserStorage);
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(ConstantReflectionInterface::class, $this->constantReflection);
        $this->assertInstanceOf(ConstantReflectionInterface::class, $this->constantReflectionInClass);
    }

    public function testGetDeclaringClass(): void
    {
        $this->assertNull($this->constantReflection->getDeclaringClass());
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->constantReflectionInClass->getDeclaringClass());
    }

    public function testGetDeclaringClassName(): void
    {
        $this->assertSame('', $this->constantReflection->getDeclaringClassName());
        $this->assertSame(ConstantInClass::class, $this->constantReflectionInClass->getDeclaringClassName());
    }

    public function testGetName(): void
    {
        $this->assertSame('SOME_CONSTANT', $this->constantReflection->getName());
        $this->assertSame('CONSTANT_INSIDE', $this->constantReflectionInClass->getName());
    }

    public function testGetShortName(): void
    {
        $this->assertSame('SOME_CONSTANT', $this->constantReflection->getShortName());
        $this->assertSame('CONSTANT_INSIDE', $this->constantReflectionInClass->getShortName());
    }

    public function testGetTypeHint(): void
    {
        $this->assertSame('string', $this->constantReflection->getTypeHint());
        $this->assertSame('int', $this->constantReflectionInClass->getTypeHint());
    }

    public function testGetValue(): void
    {
        $this->assertSame('some value', $this->constantReflection->getValue());
        $this->assertSame(55, $this->constantReflectionInClass->getValue());
    }

    public function testGetDefinition(): void
    {
        $this->assertSame("'some value'", $this->constantReflection->getValueDefinition());
        $this->assertSame('55', $this->constantReflectionInClass->getValueDefinition());
    }

    public function testIsDocumented(): void
    {
        $this->assertTrue($this->constantReflection->isDocumented());
        $this->assertTrue($this->constantReflectionInClass->isDocumented());
    }
}

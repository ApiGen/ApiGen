<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicParameterReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TokenReflection\Broker;

final class ReflectionParameterMagicTest extends TestCase
{

    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;

    /**
     * @var MagicParameterReflectionInterface
     */
    private $reflectionParameterMagic;

    /**
     * @var MagicParameterReflectionInterface
     */
    private $reflectionParameterMagicWithDefault;

    /**
     * @var MagicMethodReflectionInterface
     */
    private $reflectionParameterMagicWithTwoArgs;


    protected function setUp(): void
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

        $this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];
        $reflectionMethodMagic = $this->reflectionClass->getMagicMethods()['doAnOperation'];
        $this->reflectionParameterMagic = $reflectionMethodMagic->getParameters()['data'];

        $reflectionMethodMagic = $this->reflectionClass->getMagicMethods()['issue746'];
        $this->reflectionParameterMagicWithDefault = $reflectionMethodMagic->getParameters()['data'];

        $this->reflectionParameterMagicWithTwoArgs = $this->reflectionClass->getMagicMethods()['issue746_2'];
    }


    public function testInstance(): void
    {
        $this->assertInstanceOf(MagicParameterReflectionInterface::class, $this->reflectionParameterMagic);
    }


    public function testGetName(): void
    {
        $this->assertSame('data', $this->reflectionParameterMagic->getName());
    }


    public function testGetTypeHint(): void
    {
        $this->assertSame('\stdClass', $this->reflectionParameterMagic->getTypeHint());
    }


    public function testGetFileName(): void
    {
        $this->assertStringEndsWith('ReflectionMethod.php', $this->reflectionParameterMagic->getFileName());
    }


    public function testIsTokenized(): void
    {
        $this->assertTrue($this->reflectionParameterMagic->isTokenized());
    }


    public function testGetPrettyName(): void
    {
        $this->assertSame(
            'Project\ReflectionMethod::doAnOperation($data)',
            $this->reflectionParameterMagic->getPrettyName()
        );
    }


    public function testGetDeclaringClass(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionParameterMagic->getDeclaringClass());
    }


    public function testGetDeclaringClassName(): void
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionParameterMagic->getDeclaringClassName());
    }


    public function testGetDeclaringFunction(): void
    {
        $this->assertInstanceOf(
            MagicMethodReflectionInterface::class,
            $this->reflectionParameterMagic->getDeclaringFunction()
        );
    }


    public function testGetDeclaringFunctionName(): void
    {
        $this->assertSame('doAnOperation', $this->reflectionParameterMagic->getDeclaringFunctionName());
    }


    public function testStartLine(): void
    {
        $this->assertSame(15, $this->reflectionParameterMagic->getStartLine());
    }


    public function testEndLine(): void
    {
        $this->assertSame(15, $this->reflectionParameterMagic->getEndLine());
    }


    public function testGetDocComment(): void
    {
        $this->assertSame('', $this->reflectionParameterMagic->getDocComment());
    }


    public function testIsDefaultValueAvailable(): void
    {
        $this->assertFalse($this->reflectionParameterMagic->isDefaultValueAvailable());
    }


    public function testGetPosition(): void
    {
        $this->assertSame(0, $this->reflectionParameterMagic->getPosition());
    }


    public function testIsArray(): void
    {
        $this->assertFalse($this->reflectionParameterMagic->isArray());
    }


    public function testIsCallable(): void
    {
        $this->assertFalse($this->reflectionParameterMagic->isCallable());
    }


    public function testGetClass(): void
    {
        $this->assertNull($this->reflectionParameterMagic->getClass());
    }


    public function testGetClassName(): void
    {
        $this->assertNull($this->reflectionParameterMagic->getClassName());
    }


    public function testAllowsNull(): void
    {
        $this->assertFalse($this->reflectionParameterMagic->allowsNull());
    }


    public function testIsOptional(): void
    {
        $this->assertFalse($this->reflectionParameterMagic->isOptional());
    }


    public function testIsPassedByReference(): void
    {
        $this->assertFalse($this->reflectionParameterMagic->isPassedByReference());
    }


    public function testCanBePassedByValue(): void
    {
        $this->assertFalse($this->reflectionParameterMagic->canBePassedByValue());
    }


    public function testIsUnlimited(): void
    {
        $this->assertFalse($this->reflectionParameterMagic->isUnlimited());
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


    public function testIssue746HasDefaultValue(): void
    {
        $this->assertTrue($this->reflectionParameterMagicWithDefault->isDefaultValueAvailable());
    }

    public function testIssue746DefaultValue(): void
    {
        $this->assertEquals('null', $this->reflectionParameterMagicWithDefault->getDefaultValueDefinition());
    }

    public function testIssue764(): void
    {
        $this->assertCount(2, $this->reflectionParameterMagicWithTwoArgs->getParameters());
        $this->assertEquals('int', $this->reflectionParameterMagicWithTwoArgs->getParameter('data')->getTypeHint());
        $this->assertEquals('array', $this->reflectionParameterMagicWithTwoArgs->getParameter('type')->getTypeHint());
    }
}

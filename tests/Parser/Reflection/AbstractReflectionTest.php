<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\AbstractReflection;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Tests\MethodInvoker;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TokenReflection\Broker;

final class AbstractReflectionTest extends TestCase
{
    /**
     * @var AbstractReflection
     */
    private $reflectionClass;

    protected function setUp(): void
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

        $this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];
    }

    public function testGetName(): void
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionClass->getName());
    }

    public function testGetPrettyName(): void
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionClass->getPrettyName());
    }

    public function testIsInternal(): void
    {
        $this->assertFalse($this->reflectionClass->isInternal());
    }

    public function testIsTokenized(): void
    {
        $this->assertTrue($this->reflectionClass->isTokenized());
    }

    public function testGetFileName(): void
    {
        $this->assertStringEndsWith('ReflectionMethod.php', $this->reflectionClass->getFileName());
    }

    public function testGetStartLine(): void
    {
        $this->assertSame(10, $this->reflectionClass->getStartLine());
    }

    public function testGetEndLine(): void
    {
        $this->assertSame(40, $this->reflectionClass->getEndLine());
    }

    public function testGetParsedClasses(): void
    {
        $parsedClasses = MethodInvoker::callMethodOnObject($this->reflectionClass, 'getParsedClasses');
        $this->assertCount(1, $parsedClasses);
    }

    private function getReflectionFactory(): ReflectionFactoryInterface
    {
        $parserStorageMock = $this->createMock(ParserStorageInterface::class);
        $parserStorageMock->method('getElementsByType')
            ->willReturn(['...']);

        $configurationMock = $this->createMock(ConfigurationInterface::class);
        $configurationMock->method('getVisibilityLevel')
            ->willReturn(ReflectionProperty::IS_PUBLIC);

        return new ReflectionFactory($configurationMock, $parserStorageMock);
    }
}

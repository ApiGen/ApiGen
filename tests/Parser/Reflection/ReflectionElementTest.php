<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Tests\MethodInvoker;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TokenReflection\Broker;

final class ReflectionElementTest extends TestCase
{
    /**
     * @var ElementReflectionInterface
     */
    private $reflectionClass;

    protected function setUp(): void
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

        $this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];
    }

    public function testGetStartPosition(): void
    {
        $this->assertSame(16, $this->reflectionClass->getStartPosition());
    }

    public function testGetEndPosition(): void
    {
        $this->assertSame(69, $this->reflectionClass->getEndPosition());
    }

    public function testIsDocumented(): void
    {
        $this->assertTrue($this->reflectionClass->isDocumented());
    }

    public function testIsDeprecated(): void
    {
        $this->assertFalse($this->reflectionClass->isDeprecated());
    }

    public function testGetNamespaceName(): void
    {
        $this->assertSame('Project', $this->reflectionClass->getNamespaceName());
    }

    public function testGetPseudoNamespaceName(): void
    {
        $this->assertSame('Project', $this->reflectionClass->getPseudoNamespaceName());
    }

    public function testGetNamespacesAliases(): void
    {
        $this->assertSame([], $this->reflectionClass->getNamespaceAliases());
    }

    public function testGetDescription(): void
    {
        $this->assertSame('This is some description', $this->reflectionClass->getDescription());
    }

    public function testGetDocComment(): void
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

    public function testGetAnnotations(): void
    {
        $annotations = $this->reflectionClass->getAnnotations();
        $this->assertCount(3, $annotations);
        $this->assertArrayHasKey('property-read', $annotations);
        $this->assertArrayHasKey('method', $annotations);
        $this->assertArrayHasKey('package', $annotations);
    }

    public function testGetAnnotation(): void
    {
        $this->assertSame(['Some_Package'], $this->reflectionClass->getAnnotation('package'));
    }

    public function testHasAnnotation(): void
    {
        $this->assertTrue($this->reflectionClass->hasAnnotation('package'));
        $this->assertFalse($this->reflectionClass->hasAnnotation('nope'));
    }

    public function testGetAnnotationFromReflection(): void
    {
        $annotations = MethodInvoker::callMethodOnObject(
            $this->reflectionClass,
            'getAnnotationsFromReflection',
            [$this->reflectionClass]
        );
        $this->assertSame([], $annotations);
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
